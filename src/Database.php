<?php

    declare(strict_types=1);

    namespace src\Database;

    use PDO;
    use PDOException;

    /**
     * Class Router
     * @package src\Router
     */
    trait Database
    {

        /**
         * variable for database
         * connection instance
         * @var object $instance
         */
        private $instance;

        /**
         * bank connection configuration
         * @variables
         */
        private $_host = DATABASE_HOST;
        private $_user = DATABASE_USER;
        private $_pass = DATABASE_PASSWORD;
        private $_tabl = DATABASE_TABLENAME;

        public function __construct()
        {
            $this->up();
        }

        /**
         * Open connection
         * @throws \Exception
         */
        public function up(): void
        {

            $dsn = "mysql:host={$this->_host};dbname={$this->_tabl}";

            try {

                $pdo = new PDO($dsn, $this->_user, $this->_pass);

                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

                $this->instance = $pdo;

            } catch (PDOException $e) {

                throw new PDOException($e->getMessage(), (int) $e->getCode());

            }

        }

        /**
         * @return mixed|null
         */
        function down(): void
        {

            $this->instance = null;

            return;
        }
    }
