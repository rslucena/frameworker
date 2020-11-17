<?php


    namespace src\Controller;

    use src\Entity\UserEntity;
    use src\Provider\RenderProvider;


    class UserController extends RenderProvider implements UserEntity
    {

        public function UserPage()
        {

            $args = array('name' => 'Rodrigo Lucena');

            RenderProvider::render('home', $args);

        }

        public function UpdatePage( $args = array() )
        {

            $args = array('name' => 'Rodrigo Lucena');

            RenderProvider::render('home', $args);

        }

    }