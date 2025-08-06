<?php
require __DIR__ . '/../classes/JwtHandler.php';
class Auth extends JwtHandler
{

    protected $db;
    protected $headers;
    protected $token;
    public function __construct($db, $headers)
    {
        parent::__construct();
        $this->db = $db;
        $this->headers = $headers;
    }

    public function isAuth()
    {
        if (array_key_exists('Authorization', $this->headers) && !empty(trim($this->headers['Authorization']))) {
            $this->token = explode(" ", trim($this->headers['Authorization']));
            if (isset($this->token[1]) && !empty(trim($this->token[1]))) {

                $data = $this->_jwt_decode_data($this->token[1]);

                if (isset($data['auth']) && isset($data['data']->UserId) && $data['auth']) {
                    $user = $this->fetchUser($data['data']->UserId);
                    return $user;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    protected function fetchUser($UserId)
    {
        try {
            $fetch_user_by_id = "SELECT *
            FROM t_users WHERE UserId =:UserId";
            $query_stmt = $this->db->prepare($fetch_user_by_id);
            $query_stmt->bindValue(':UserId', $UserId, PDO::PARAM_INT);
            $query_stmt->execute();

            if ($query_stmt->rowCount()) :
                $row = $query_stmt->fetch(PDO::FETCH_ASSOC);
                return [
                    'success' => 1,
                    'status' => 200,
                    'user' => $row
                ];
            else :
                return null;
            endif;
        } catch (PDOException $e) {
            return null;
        }
    }
}
