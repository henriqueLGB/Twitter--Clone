<?php

namespace App\Controllers;

//os recursos do miniframework

use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

    public function timeline(){

        $this->validarAutentificacao();

        $tweet = Container::getModel('Tweet');

        $tweet->__set('id_usuario',$_SESSION['id']);

        $tweets = $tweet->getAll();

        $this->view->tweets = $tweets;

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id',$_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->render('timeline');

    }

    public function tweet(){

        $this->validarAutentificacao();
        
        $tweet = Container::getModel('Tweet');

        $tweet->__set('tweet',$_POST['tweet']);
        $tweet->__set('id_usuario',$_SESSION['id']);

        $tweet->salvar();

        header('Location: /timeline');

    }

    public function validarAutentificacao(){

        session_start();

        if(!isset($_SESSION['id']) || empty($_SESSION['id']) || !isset($_SESSION['nome']) || empty($_SESSION['nome'])){
            header('Location: /?login=erro');
        }

    }

    public function quemSeguir(){

        $this->validarAutentificacao();

        $pesquisaPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';
        $usuarios    = array();

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id',$_SESSION['id']);

        if(!empty($pesquisaPor)){
            
            $usuario->__set('nome',$pesquisaPor);
            $usuarios = $usuario->getAll();

        }

        
        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->view->usuarios = $usuarios;

        $this->render('quemSeguir');

    }

    public function acao(){

        $this->validarAutentificacao();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id',$_SESSION['id']);

        switch($acao){

            case 'seguir':
                $usuario->seguirUsuario($id_usuario_seguindo);
            break;

            case 'deixar_seguir':
                $usuario->deixarSeguirUsuario($id_usuario_seguindo);
            break;

        }

        header('Location: /quem_seguir');
    }

    public function removerTwitter(){

        $this->validarAutentificacao();

        $tweet = Container::getModel('Tweet');

        $tweet->removerTwitter($_GET['remover']);

        header('Location: /timeline');

    }

}