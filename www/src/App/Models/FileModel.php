<?php
namespace App\Models;

use App\Db\Connect;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FileModel extends Model
{

    /**
     * Путь до общей директории, где хранятся файлы
     * @var string 
     */
    protected $pathFilesDir;

    /**
     * Разрешенные расширения файлов
     */
    const ALLOWED_EXTENTIONS = ['png', 'jpeg', 'jpg', 'txt', 'doc', 'docx'];


    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->pathFilesDir = __DIR__ . '/../../../files/';
    }
    
    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Создание и сохранение нового файла.
     * @param RequestInterface $request
     * @return bool
     */
    public function saveFile(RequestInterface $request): bool
    {
        $fileName = $request->getQueryParams()['filename'];
        if (empty($fileName)) {
            return false;
        }
        $data = $this->getExtAndNameFile($fileName);
        if (empty($data['name']) || empty($data['ext']) || !$this->checkEXtention($data['ext'])) {
            $this->errorMessage = 'Не корректное имя файла или тип';
            return false;
        }
        if ($this->checkFileInDB($data['name'], $data['ext'])) {
            $this->errorMessage = 'Такой файл уже сушествует';
            return false;
        }
        $hash = md5($data['name'] . $this->generateRandomString(6));
        $file = fopen($this->pathFilesDir . $hash, 'w');
        $content = file_get_contents('php://input');
        $result = fwrite($file, $content);
        fclose($file);
        if ($result) {
            return $this->saveDataToDataBase($data, $hash);
        } else {
            $this->errorMessage = 'Не удалось добавить файл в ФС';
            return false;
        }
    }
    /**
     * Обновление данных
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function updateFile (RequestInterface $request)
    {
        $id = $request->getQueryParams()['id'];
        $data = $this->getFileData((int) $id);
        $file = fopen($this->pathFilesDir . $data['hash'], 'w');
        $content = file_get_contents('php://input');
        $result = fwrite($file, $content);
        fclose($file);
        if ($result) {
            $name = $request->getQueryParams()['filename'];
            $fileName = is_null($name) ? $data['name'] : $name;
            return $this->updateDataToDataBase($id, $fileName);
        } else {
            $this->errorMessage = 'Не удалось обновить файл в ФС';
            return false;
        } 
    }
    
    /**
     * Обновление данных в БД
     * @param int $id
     * @param string $fileName
     * @return bool
     */
    private function updateDataToDataBase (int $id, string $fileName): bool
    {
        $result = false;
        $connetion = Connect::getInstance()->getConnection();
        $stmt = $connetion->prepare('UPDATE files SET name = :name, update_date = now() WHERE id = :id AND user_id = :userId;');
        $result = $stmt->execute(['userId' => $_SESSION['userId'], 'id' => $id, 'name' => $fileName]);
        if (!$result) {
            $this->errorMessage = 'Не удалось добавить файл в БД';
        }
        return $result;
    }

    /**
     * Получение списка фалов пользователя
     * @return array
     */
    public function geFilesList (): array
    {
        $result = [];
        $connetion = Connect::getInstance()->getConnection();
        $stmt = $connetion->prepare('SELECT `id`, `create_date` as `create`, `update_date` as `update`, `ext` '
            . 'FROM files '
            . 'WHERE  user_id = :userId '
            . 'ORDER BY update_date ASC');
        $stmt->execute(['userId' => $_SESSION['userId']]);
        foreach ($stmt as $row) {
            $result[] = $row;
        }
        return $result;
    }
    
    /**
     * Получение данны по файлу из БД
     * @param int $id
     * @return array
     */
    private function getFileData (int $id): array
    {
        $result = [];
        $connetion = Connect::getInstance()->getConnection();
        $stmt = $connetion->prepare('SELECT `name`, `hash_name` as `hash`, `create_date` as `create`, `update_date` as `update`, `ext` FROM files WHERE  user_id = :userId AND id = :id');
        $stmt->execute(['userId' => (int) $_SESSION['userId'], 'id' => $id]);
        foreach ($stmt as $row) {
            $result = $row;
        }
        return $result;
    }
    /**
     * Прововерка на сушествование файла в  БД
     * @param int $id
     * @return array
     */
    private function checkFileInDB (string $name, string $ext): bool
    {
        $result = false;
        $connetion = Connect::getInstance()->getConnection();
        $stmt = $connetion->prepare(
                'SELECT `id` '
                . 'FROM files WHERE  user_id = :userId AND ext = :ext AND ext = :ext'
                );
        $stmt->execute(['userId' => (int) $_SESSION['userId'], 'name' => $name, 'ext' => $ext]);
        foreach ($stmt as $row) {
            $result = true;
            break;
        }
        return $result;
    }
    
    /**
     * Получение данных по файлу
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return type
     */
    public function getFile(RequestInterface $request, ResponseInterface $response)
    {
        $id = $request->getQueryParams()['id'];
        $data = $this->getFileData((int) $id);
        if (empty($data)) {
            return $response->withStatus(404);
        }
        $tmp = file_get_contents($this->pathFilesDir . $data['hash']);
        $deflateContext = deflate_init(ZLIB_ENCODING_GZIP);
        $compressed = deflate_add($deflateContext, (string) $tmp, ZLIB_FINISH);
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $compressed);
        rewind($stream);
        return $response
                ->withHeader('Content-Type', 'application/octet-stream')
                ->withHeader('Content-Transfer-Encoding', 'binary')
                ->withHeader('Content-Disposition', 'Attachment; filename='. $data['name'] . '.' . $data['ext'])
                ->withHeader('Content-Encoding', 'gzip')
                ->withHeader('Content-Length', strlen($compressed))
                ->withBody(new \Slim\Http\Stream($stream));
    }

    /**
     * Cохранение данных по добавленному файлу в БД
     * @param array $data 
     * @param string $hash
     * @return bool
     */
    private function saveDataToDataBase(array $data, string $hash): bool
    {
        $connetion = Connect::getInstance()->getConnection();
        $stmt = $connetion->prepare('INSERT INTO files (`name`, `ext`, `hash_name`, `user_id`) VALUES (:name, :ext, :hashName, :userId)');
        $result = $stmt->execute([
            'name' => $data['name'],
            'ext' => $data['ext'],
            'hashName' => $hash,
            'userId' => (int) $_SESSION['userId'],
        ]);
        if (!$result) {
            $this->errorMessage = 'Не удалось добавить файл в БД';
        }
        return $result;
    }

    /**
     * Получение расщирения и именини файла 
     * @param string $fileName
     * @return array
     */
    public function getExtAndNameFile(string $fileName): array
    {
        $data = (explode('.', $fileName));
        $ext = array_pop($data);
        $name = implode('.', $data);
        return compact('name', 'ext');
    }
    
    /**
     * Проверка типа файла
     * @param string $ext
     * @return bool
     */
    public function checkEXtention(string $ext): bool
    {
        return in_array($ext, self::ALLOWED_EXTENTIONS);
    }
}
