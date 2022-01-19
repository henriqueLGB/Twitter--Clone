<?php

    namespace App\Models;
    use MF\Model\Model;

    class Tweet extends Model{

        private $id;
        private $id_usuario;
        private $tweet;
        private $data;


        public function __get($atributo){
            return $this->$atributo;
        }

        public function __set($atributo,$valor){
            $this->$atributo = $valor;
        }


        //Salvar
        public function salvar(){

            $query = "INSERT INTO tweets(id_usuario,tweet) VALUES (:id_usuario,:tweet)";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario',$this->__get('id_usuario'));
            $stmt->bindValue(':tweet',$this->__get('tweet'));

            $stmt->execute();

            return $this;

        }   

        //Recuperar
        public function getAll(){

            $query ="SELECT 
                        tw.id,
                        tw.id_usuario,
                        usr.nome,
                        tw.tweet,
                        DATE_FORMAT(tw.data,'%d/%m/%Y %H:%i') as data 
                    FROM 
                        tweets tw
                        LEFT JOIN usuarios usr ON (
                            usr.id = tw.id_usuario
                        )
                    WHERE 
                        tw.id_usuario = :id_usuario
                        OR tw.id_usuario IN ( SELECT id_usuario_seguindo FROM usuarios_seguidores WHERE id_usuario = :id_usuario)
                    ORDER BY
                        tw.data DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario',$this->__get('id_usuario'));
            $stmt->execute();


            return $stmt->fetchAll(\PDO::FETCH_ASSOC);


        }

        //Deletar
        public function removerTwitter($id_tweet){

            $query = "DELETE FROM tweets WHERE id = :id_tweet";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_tweet',$id_tweet);
            $stmt->execute();

            return true;

        }
 
    }


?>