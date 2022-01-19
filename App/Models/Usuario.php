<?php

    namespace App\Models;
    use MF\Model\Model;

    class Usuario extends Model{

        private $id;
        private $nome;
        private $email;
        private $senha;


        public function __get($atributo){
            return $this->$atributo;
        }

        public function __set($atributo,$valor){
            $this->$atributo = $valor;
        }

        //Salvar
        public function salvar(){

            $qry = "INSERT INTO usuarios(nome,email,senha)VALUES(:nome,:email,:senha)";
            
            $stmt = $this->db->prepare($qry);
            $stmt->bindValue(':nome',$this->nome);
            $stmt->bindValue(':email',$this->email);
            $stmt->bindValue(':senha',$this->senha); //md5() hash 32 caracteres

            $stmt->execute();

            return $this;
        }

        //Validar se um cadastro pode ser feito
        public function validarCadastro(){

            $valido = true;

            if(strlen($this->__get('nome')) < 3){
                $valido = false;
            }

            if(strlen($this->__get('email')) < 3){
                $valido = false;
            }

            if(strlen($this->__get('senha')) < 3){
                $valido = false;
            }

            return $valido;
            
        }

        //Recuperar um usuário por e-mail
        public function getUsuarioPorEmail(){

            $query = "SELECT nome,email FROM usuarios WHERE email = :email";

            $stmt  = $this->db->prepare($query);
            $stmt->bindValue(':email',$this->__get('email'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        }

        public function autenticar(){

            $query = "SELECT id, nome, email FROM usuarios WHERE email = :email AND senha = :senha";
            $stmt  = $this->db->prepare($query);
            $stmt->bindValue(':email',$this->__get('email'));
            $stmt->bindValue(':senha',$this->__get('senha'));
            $stmt->execute();

            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            if(!empty($usuario['id']) && !empty($usuario['nome'])){
                $this->__set('id',$usuario['id']);
                $this->__set('nome',$usuario['nome']);
            }

            return $this;
        }

        public function getAll(){

            $query ="SELECT 
                        usr.id, 
                        usr.nome, 
                        usr.email,
                        (
                            SELECT
                                COUNT(*)
                            FROM
                                usuarios_seguidores usrs
                            WHERE
                                usrs.id_usuario = :id_usuario
                                AND usrs.id_usuario_seguindo  = usr.id

                        ) as seguindo_sn 
                    FROM 
                        usuarios usr
                    WHERE
                        usr.nome LIKE :nome 
                        AND usr.id <> :id_usuario";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome','%'.$this->__get('nome').'%');
            $stmt->bindValue(':id_usuario',$this->__get('id'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        }

        public function seguirUsuario($id_usuario_seguindo){
            
            $query = "INSERT INTO usuarios_seguidores (id_usuario,id_usuario_seguindo) VALUES (:id_usuario,:id_usuario_seguindo)";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario',$this->__get('id'));
            $stmt->bindValue(':id_usuario_seguindo',$id_usuario_seguindo);
            $stmt->execute();

            return true;
            
        }

        public function deixarSeguirUsuario($id_usuario_seguindo){
           
            $query = "DELETE FROM usuarios_seguidores WHERE id_usuario = :id_usuario AND id_usuario_seguindo = :id_usuario_seguindo";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario',$this->__get('id'));
            $stmt->bindValue(':id_usuario_seguindo',$id_usuario_seguindo);
            $stmt->execute();

            return true;

        }

        //Informações do Usuário
        public function getInfoUsuario(){

            $query = "SELECT nome FROM usuarios WHERE id = :id_usuario";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario',$this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);

        }

        //Total de Tweets
        public function getTotalTweets(){

            $query = "SELECT COUNT(*) AS total_tweet FROM tweets WHERE id_usuario = :id_usuario";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario',$this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);

        }

        //Total de Usuários que estamos seguindo
        public function getTotalSeguindo(){

            $query = "SELECT COUNT(*) AS total_seguindo  FROM usuarios_seguidores WHERE id_usuario = :id_usuario";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario',$this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);

        }

        //Total de seguidores
        public function getTotalSeguidores(){

            $query = "SELECT COUNT(*) AS total_seguidores  FROM usuarios_seguidores WHERE id_usuario_seguindo = :id_usuario";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario',$this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);

        }


    }


?>