<?php

namespace App\Core;

use App\Model\Comment;
use App\Model\Post;
use App\Model\User;
use App\Model\User as UserModel;
use DateInterval;
use DateTime;
use Exception;
use PDO;

abstract class BaseSQL
{
    private $pdo;
    private $table;
    private $data = [];


    public function __construct()
    {

        try {
            $this->pdo = Db::connect();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Logger::writeLog("Error with DB Connection, $error");
            die("Erreur SQL : " . $error);
        }
       
        $classExploded = explode("\\", get_called_class());
        $this->table = DBPREFIXE . strtolower(end($classExploded));
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE id=:id ";
        $queryPrepared = $this->pdo->prepare($sql, ['id' => $id]);
        return $queryPrepared->fetchObject(get_called_class());
        
    }


    public function save()
    {

        $columns = get_object_vars($this);
        $varsToExclude = get_class_vars(get_class());
        $columns = array_diff_key($columns, $varsToExclude);
        $columns = array_filter($columns);


        if (!is_null($this->getId())) {
            foreach ($columns as $key => $value) {
                $setUpdate[] = $key . "=:" . $key;
            }
            $sql = "UPDATE " . $this->table . " SET " . implode(",", $setUpdate) . " WHERE id=" . $this->getId();
        } else {
            $sql = "INSERT INTO " . $this->table . " (" . implode(",", array_keys($columns)) . ")
                    VALUES (:" . implode(",:", array_keys($columns)) . ")";
        }

        $this->pdo->prepare($sql, $columns);
     
    }

    public function findAll(?string $class = '')
    {
        $sql = "SELECT * FROM " . $this->table;

       $queryPrepared = $this->pdo->prepare($sql);

        if ($class !== '') return $queryPrepared->fetchAll(PDO::FETCH_CLASS, get_called_class());
        return $queryPrepared->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllBy(array $params, string $opt_table = null, ?string $class = '')
    {
        foreach ($params as $key => $value) {
            $where[] = $key . "=:" . $key;
        }

        if (!is_null($opt_table)) {
            $sql = "SELECT * FROM " . DB_PREFIXE . "_" . strtolower($opt_table) . " WHERE " . (implode(" AND ", $where));
        } else {
            $sql = "SELECT * FROM " . $this->table . " WHERE " . (implode(" AND ", $where));
        }
        $queryPrepared = $this->pdo->prepare($sql);
        $queryPrepared->execute($params);
        if ($class !== '') return $queryPrepared->fetchAll(PDO::FETCH_CLASS, get_called_class());
        return $queryPrepared->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findOneBy(array $params): array
    {
        foreach ($params as $key => $value) {
            $where[] = $key . "=:" . $key;
        }

        $sql = "SELECT * FROM " . $this->table . " WHERE " . (implode(" AND ", $where));
        $$queryPrepared = $this->pdo->prepare($sql, $params);
        $data = $queryPrepared->fetch(PDO::FETCH_ASSOC);
        return empty($data) ? [] : $data;
    }

    public function findByColumn(array $columns, array $params): array
    {
        $select = $columns;

        foreach ($params as $key => $value) {
            if (strstr($key, 'date')) {
                # date comparaison
                $where[] = $key . ">:" . $key;
            } else {
                $where[] = $key . "=:" . $key;
            }
        }

        $sql = "SELECT " . implode(",", $select) . " FROM " . $this->table . " WHERE " . (implode(" AND ", $where));
       $queryPrepared = $this->pdo->prepare($sql, $params);
        $data = $queryPrepared->fetch(PDO::FETCH_ASSOC);
        return empty($data) ? [] : $data;
    }

    public function findAllByColumn(array $columns, array $params): array
    {
        $select = $columns;

        foreach ($params as $key => $value) {
            if (strstr($key, 'date')) {
                # date comparaison
                $where[] = $key . ">:" . $key;
            } else {
                $where[] = $key . "=:" . $key;
            }
        }

        $sql = "SELECT " . implode(",", $select) . " FROM " . $this->table . " WHERE " . (implode(" AND ", $where));
        $queryPrepared = $this->pdo->prepare($sql, $params);
        $data = $queryPrepared->fetchAll(PDO::FETCH_ASSOC);
        return empty($data) ? ["user" => false] : $data;
    }

    public function findUserById(string $id) {
        $sql = 'SELECT id, username, email, first_name, last_name, role, registered_at, updated_at, activated, gender, blocked, blocked_until, birth FROM ' . DB_PREFIXE . "_user" . ' WHERE id = ?';
        $params = [$id];
        $queryPrepared = $this->pdo->prepare($sql, $params);
        return $queryPrepared->fetchObject(User::class);
    }

    public function deleteOne()
    {
        if (isset($_POST['id']) && !empty($_POST['id'])) {

            $id = strip_tags($_POST['id']);

            $sql = "DELETE FROM `" . $this->table . "` WHERE `id`=:id";
            $queryPrepared = $this->pdo->prepare($sql, ['id' => $id]);
            $queryPrepared->bindValue(':id', $id, PDO::PARAM_INT);

            return true;
        }
    }

    public function verifieMailUnique()
    {
        $column = array_diff_key(
            get_object_vars($this),
            get_class_vars(get_class())
        );
        $sql = $this->pdo->prepare("SELECT count(email) as nb FROM " . $this->table . " WHERE email = :email");

        if ($sql->execute(['email' => $column["email"]])) {
            $obj = $sql->fetch();
            return $obj["nb"];
        }

        return false;
    }


    public function getTable(): string
    {
        return $this->table;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * Cette fonction permet de récupérer une session à partir d'un token
     * @param string $token Le token
     * @return array|null La session ou null si le token est invalide ou expiré
     */
    public function sessionWithToken(string $token): ?array
    {
        return $this->findByColumn(["id", "token", "user_id", "expiration_date"], ["token" => $token, "expiration_date" => " NOW()"]);
    }

}
