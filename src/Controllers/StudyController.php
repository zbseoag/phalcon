<?php
declare(strict_types=1);

namespace Invo\Controllers;
use Invo\Forms\RegisterForm;

class StudyController extends Controller {

    public function initialize(){

        parent::initialize();
        $this->tag->setTitle('查看对象');
    }

    public function index(): void{

        $this->dispatcher->forward('index/index');


    }

    public function register() {

        $user = new Users();
        $success = $user->save($this->request->getPost(), ["name", "email",]);

        if ($success) {
            echo "Thanks for registering!";
        } else {
            echo "Sorry, the following problems were generated: ";
            $messages = $user->getMessages();

            foreach ($messages as $message) {
                echo $message->getMessage(), "<br/>";
            }
        }
        $this->view->disable();
    }

    /**
     * 通过Phalcon运行的兼容PSR-4的文件加载器
     */
    public function loader(): void{

        define('BASE_PATH', dirname(__DIR__));
        define('APP_PATH', BASE_PATH . '/app');

        $loader = new \Phalcon\Loader();

        $loader->registerDirs([
            APP_PATH . '/controllers/',
            APP_PATH . '/models/',
        ]);
        //        'controllersDir' => 'app/controllers/',
        //        'modelsDir'      => 'app/models/',
        //        'pluginsDir'     => 'app/plugins/',
        //        'formsDir'       => 'app/forms/',
        //        'libraryDir'     => 'app/library/',
        $loader->register();

    }


    public function component(): void{
        try {

            $di = new \Phalcon\Di\FactoryDefault();
            $di->set('view2', function () {
                $view = new \Phalcon\Mvc\View();
                $view->setViewsDir(APP_PATH . '/views/');
                return $view;
            });

            $di->set('url2', function () {
                $url = new \Phalcon\Mvc\Url();
                $url->setBaseUri('/');
                return $url;
            });

            $application = new \Phalcon\Mvc\Application($di);
            $response = $application->handle($_SERVER['REQUEST_URI']);
            $response->send();

        } catch (\Exception $e) {
            echo $e->getMessage() . '<br>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        }

    }

    public function profiler(): void{

        $profiles = $this->di->get('profiler')->getProfiles();

        if($profiles) foreach ($profiles as $profile) {
            echo "SQL语句: ", $profile->getSQLStatement(), "\n";
            echo "开始时间: ", $profile->getInitialTime(), "\n";
            echo "结束时间: ", $profile->getFinalTime(), "\n";
            echo "消耗时间: ", $profile->getTotalElapsedSeconds(), "\n";
        }

        if($profiles) echo $this->di->get('profiler')->getLastProfile()->getSQLStatement();
        $this->view->disable();

    }

    public function query(): void {

//        $manager = new \Phalcon\Events\Manager();
//        $application->setEventsManager($manager);
//        $manager->attach('application', function (\Phalcon\Events\Event $event, $application) {
//            p($event->getType());
//        });

        $sql = 'SELECT * FROM `companies`';
        $connection = $this->di->get('db');
        $result = $connection->query($sql);
        $result->setFetchMode(Phalcon\Db::FETCH_ASSOC);

        $result->seek(2); // 寻找第三排
        $robot = $result->fetch();
        echo $result->numRows();  // 计算结果集

        while ($item = $result->fetch()) {
            p($item);
        }

        //获取数组中的所有行
        $result = $connection->fetchAll($sql);
        foreach ($result as $item) {
            p($item);
        }
        // 只获得第一行
        $result = $connection->fetchOne($sql);


        // 使用数字占位符绑定
        $sql    = 'SELECT * FROM robots WHERE name = ? ORDER BY name';
        $result = $connection->query($sql, ['Wall-E']);

        // 与命名占位符绑定
        $sql     = 'INSERT INTO `robots`(name`, year) VALUES (:name, :year)';
        $success = $connection->query($sql, ['name' => 'Astro Boy', 'year' => 1952]);

        $phql = "SELECT * FROM Store\Robots WHERE id > :id:";
        $robots = $this->modelsManager->executeQuery($phql, ['id' => 100]);

        $phql = "SELECT * FROM Store\Robots LIMIT :number:";
        $robots = $this->modelsManager->executeQuery($phql, ['number' => 10], \Phalcon\Db\Column::BIND_PARAM_INT);

        $phql = "SELECT * FROM Store\Robots LIMIT {number:int}";
        $robots = $this->modelsManager->executeQuery($phql, ['number' => 10]);

        $phql = "SELECT * FROM Store\Robots WHERE name <> {name:str}";
        $robots = $this->modelsManager->executeQuery($phql, ['name' => $name]);

        $phql = "SELECT * FROM Store\Robots WHERE name <> {name}";
        $robots = $this->modelsManager->executeQuery($phql, ['name' => $name]);

        $phql = "SELECT * FROM Store\Robots WHERE id IN ({ids:array})";
        $robots = $this->modelsManager->executeQuery($phql, ['ids' => [1, 2, 3, 4]]);

        $book = '
        绑定类型	绑定类型常量	                示例
        str	        Column::BIND_PARAM_STR	    {name:str}
        int	        Column::BIND_PARAM_INT	    {number:int}
        double	    Column::BIND_PARAM_DECIMAL	{price:double}
        bool	    Column::BIND_PARAM_BOOL	    {enabled:bool}
        blob	    Column::BIND_PARAM_BLOB	    {image:blob}
        null	    Column::BIND_PARAM_NULL	    {exists:null}
        array	    	                        {codes:array}
        array-str		                        {names:array-str}
        array-int	                    	    {flags:array-int}
        ';

        $number = '100';
        $robots =  $this->modelsManager->executeQuery('SELECT * FROM Some\Robots LIMIT {number:int}', ['number' => (int) $number]);
        \Phalcon\Db::setup(['forceCasting' => true]);

        $book = '
        绑定类型	                动作
        Column::BIND_PARAM_STR	    将值转换为原生PHP字符串
        Column::BIND_PARAM_INT	    将值转换为原生PHP整型
        Column::BIND_PARAM_BOOL	    将值转换为原生PHP布尔值
        Column::BIND_PARAM_DECIMAL	将值转换为原生PHP变量
        ';

        \Phalcon\Mvc\Model::setup(['castOnHydrate' => true]);


        // 使用原始SQL语句插入数据
        $sql     = "INSERT INTO `robots`(`name`, `year`) VALUES ('Astro Boy', 1952)";
        $success = $connection->execute($sql);

        // 占位符
        $sql     = 'INSERT INTO `robots`(`name`, `year`) VALUES (?, ?)';
        $success = $connection->execute($sql, ['Astro Boy', 1952]);

        // 动态生成必要的SQL
        $success = $connection->insert('robots', ['Astro Boy', 1952], ['name', 'year']);

        // 动态生成必要的SQL（另一种语法）
        $success = $connection->insertAsDict('robots', ['name' => 'Astro Boy', 'year' => 1952]);

        // 使用原始SQL语句更新数据
        $sql     = "UPDATE `robots` SET `name` = 'Astro boy' WHERE `id` = 101";
        $success = $connection->execute($sql);

        // 占位符
        $sql     = "UPDATE `robots` SET `name` = ? WHERE `id` = ?";
        $success = $connection->execute($sql, ['Astro Boy', 101]);

        // 动态生成必要的SQL
        $success = $connection->update('robots', ['name'], ['New Astro Boy'], 'id = 101');//在这种情况下，id 值不会被转义
        // 动态生成必要的SQL（另一种语法）
        $success = $connection->updateAsDict('robots', ['name' => 'New Astro Boy'], 'id = 101');

        // 转义条件
        $success = $connection->update('robots',
            ['name'],
            ['New Astro Boy'],
            [
                'conditions' => 'id = ?',
                'bind'       => [101],
                'bindTypes'  => [PDO::PARAM_INT], // 可选参数
            ]
        );

        $success = $connection->updateAsDict('robots',
            ['name' => 'New Astro Boy'],
            [
                'conditions' => 'id = ?',
                'bind'       => [101],
                'bindTypes'  => [PDO::PARAM_INT], // Optional parameter
            ]
        );

        // 使用原始SQL语句删除数据
        $sql     = 'DELETE `robots` WHERE `id` = 101';
        $success = $connection->execute($sql);

        // 占位符
        $sql     = 'DELETE `robots` WHERE `id` = ?';
        $success = $connection->execute($sql, [101]);

        // 动态生成必要的SQL
        $success = $connection->delete('robots', 'id = ?',
            [101]
        );


        try {
            // 开始事务
            $connection->begin();
            // 执行一些SQL语句
            $connection->execute('DELETE `robots` WHERE `id` = 101');
            $connection->execute('DELETE `robots` WHERE `id` = 102');

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }


        try {
            // 开始事务
            $connection->begin();
            $connection->execute('DELETE `robots` WHERE `id` = 101');

            try {
                // 开始嵌套事务
                $connection->begin();

                // 将这些SQL语句执行到嵌套事务中
                $connection->execute('DELETE `robots` WHERE `id` = 102');
                $connection->execute('DELETE `robots` WHERE `id` = 103');

                // 创建一个保存点
                $connection->commit();
            } catch (Exception $e) {
                // 发生错误，释放嵌套事务
                $connection->rollback();
            }

            // 继续，执行更多SQL语句
            $connection->execute('DELETE `robots` WHERE `id` = 104');

            // 如果一切顺利，commit
            $connection->commit();
        } catch (Exception $e) {
            // 发生了异常回滚事务
            $connection->rollback();
        }

        $book = '
            事件名称	        触发条件	                    能停止活动操作
            afterConnect	    成功连接到数据库系统后	        No
            beforeQuery	        在将SQL语句发送到数据库执行之前	Yes
            afterQuery	        将SQL语句发送到数据库执行后	    No
            beforeDisconnect	在关闭临时数据库连接之前	        No
            beginTransaction	在开始事务之前	                No
            rollbackTransaction	在事务被回滚之前	                No
            commitTransaction	在提交事务之前	                No
        ';

        $eventsManager = new \Phalcon\Events\Manager();

        $connection = new \Phalcon\Db\Adapter\Pdo\Mysql([
            'host'     => 'localhost',
            'username' => 'root',
            'password' => 'secret',
            'dbname'   => 'invo',
        ]);

        // 将eventsManager分配给db adapter实例
        $connection->setEventsManager($eventsManager);

        $eventsManager->attach('db:beforeQuery', function (\Phalcon\Events\Event $event, $connection) {
                $sql = $connection->getSQLStatement();

                // 检查SQL语句中的恶意词
                if (preg_match('/DROP|ALTER/i', $sql)) {
                    // 应用程序中不允许DROP / ALTER操作，
                    // 这必须是SQL注入！
                    return false;
                }
                // It's OK
                return true;
            }
        );



        // 获取test_db数据库上的表
        $tables = $connection->listTables('test_db');

        // 数据库中是否有表'robots'？
        $exists = $connection->tableExists('robots');

        // 获取'robots'字段的名称，数据类型和特殊功能
        $fields = $connection->describeColumns('robots');
        foreach ($fields as $field) {
            echo 'Column Type: ', $field['Type'];
        }

        // 获取'robots'表上的索引
        $indexes = $connection->describeIndexes('robots');
        foreach ($indexes as $index) {
            print_r($index->getColumns());
        }

        // 在'robots'表上获取外键
        $references = $connection->describeReferences('robots');
        foreach ($references as $reference) {
            print_r($reference->getReferencedColumns()); // 打印外键列
        }

        // 获取有关test_db数据库的视图
        $tables = $connection->listViews('test_db');

        // 数据库中是否有'robots' 视图？
        $exists = $connection->viewExists('robots');


        $connection->createTable('robots', null,
            [
                'columns' => [
                    new \Phalcon\Db\Column('id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'size'          => 10,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'primary'       => true,
                        ]
                    ),
                    new \Phalcon\Db\Column('name',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 70,
                            'notNull' => true,
                        ]
                    ),
                    new \Phalcon\Db\Column('year',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'size'    => 11,
                            'notNull' => true,
                        ]
                    ),
                ]
            ]
        );


        // 增加新列
        $connection->addColumn('robots', null,
            new Column('robot_type', [
                    'type'    => Column::TYPE_VARCHAR,
                    'size'    => 32,
                    'notNull' => true,
                    'after'   => 'name',
                ]
            )
        );

// 修改现有列
        $connection->modifyColumn('robots', null,
            new Column('name',
                [
                    'type'    => Column::TYPE_VARCHAR,
                    'size'    => 40,
                    'notNull' => true,
                ]
            )
        );

        // 删除列 'name'
        $connection->dropColumn('robots', null, 'name');

        //从活动数据库中删除表'robots'
        $connection->dropTable('robots');

        //从数据库'machines'中删除表'robots'
        $connection->dropTable('robots', 'machines');

    }

    public function phql(){

        // 实例化查询
        $query = new \Phalcon\Mvc\Model\Query('SELECT * FROM Cars', $this->getDI());

        // 执行返回结果的查询（如果有）
        $cars = $query->execute();


        //从控制器或视图中，使用注入的models manager（Phalcon\Mvc\Model\Manager）
        // 执行简单的查询
        $query = $this->modelsManager->createQuery('SELECT * FROM Cars');
        $cars  = $query->execute();

        // 带有绑定参数
        $query = $this->modelsManager->createQuery('SELECT * FROM Cars WHERE name = :name:');
        $cars  = $query->execute(['name' => 'Audi']);

        // 执行简单的查询
        $cars = $this->modelsManager->executeQuery('SELECT * FROM Cars');

        // 带有绑定参数
        $cars = $this->modelsManager->executeQuery('SELECT * FROM Cars WHERE name = :name:',
            ['name' => 'Audi']
        );

        $manager = $this->modelsManager;

        $query = $manager->createQuery('SELECT * FROM Cars ORDER BY Cars.name');

        $query = $manager->createQuery('SELECT Cars.name FROM Cars ORDER BY Cars.name');

        $phql  = 'SELECT * FROM Formula\Cars ORDER BY Formula\Cars.name';
        $query = $manager->createQuery($phql);

        $phql  = 'SELECT Formula\Cars.name FROM Formula\Cars ORDER BY Formula\Cars.name';
        $query = $manager->createQuery($phql);

        $phql  = 'SELECT c.name FROM Formula\Cars c ORDER BY c.name';
        $query = $manager->createQuery($phql);

        $phql = 'SELECT c.name FROM Cars AS c WHERE c.brand_id = 21 ORDER BY c.name LIMIT 100';
        $query = $manager->createQuery($phql);

        $phql = 'SELECT c.* FROM Cars AS c ORDER BY c.name';
        $cars = $manager->executeQuery($phql);

        foreach ($cars as $car) {
            echo 'Name: ', $car->name, "\n";
        }

        //等同于
        $cars = Cars::find(['order' => 'name']);
        foreach ($cars as $car) {
            echo 'Name: ', $car->name, "\n";
        }

        $phql = "SELECT CONCAT(c.id, ' ', c.name) AS id_name FROM Cars AS c ORDER BY c.name";
        $cars = $manager->executeQuery($phql);
        foreach ($cars as $car) {
            echo $car->id_name, "\n";
        }

        //在这种情况下的结果是对象 Phalcon\Mvc\Model\Resultset\Complex。这允许一次访问完整对象和标量
        $phql = 'SELECT c.price*0.16 AS taxes, c.* FROM Cars AS c ORDER BY c.name';
        $result = $manager->executeQuery($phql);
        foreach ($result as $row) {
            echo 'Name: ', $row->cars->name, "\n";
            echo 'Price: ', $row->cars->price, "\n";
            echo 'Taxes: ', $row->taxes, "\n";
        }

        //默认为 INNER JOIN。您可以在查询中指定JOIN的类型：
        $phql = 'SELECT Cars.*, Brands.* FROM Cars INNER JOIN Brands';
        $rows = $manager->executeQuery($phql);

        $phql = 'SELECT Cars.*, Brands.* FROM Cars LEFT JOIN Brands';
        $rows = $manager->executeQuery($phql);

        $phql = 'SELECT Cars.*, Brands.* FROM Cars LEFT OUTER JOIN Brands';
        $rows = $manager->executeQuery($phql);

        $phql = 'SELECT Cars.*, Brands.* FROM Cars CROSS JOIN Brands';
        $rows = $manager->executeQuery($phql);

        // 简单条件
        $phql = 'SELECT * FROM Cars WHERE Cars.name = "Lamborghini Espada"';
        $cars = $manager->executeQuery($phql);

        $phql = 'SELECT * FROM Cars WHERE Cars.price > 10000';
        $cars = $manager->executeQuery($phql);

        $phql = 'SELECT * FROM Cars WHERE TRIM(Cars.name) = "Audi R8"';
        $cars = $manager->executeQuery($phql);

        $phql = 'SELECT * FROM Cars WHERE Cars.name LIKE "Ferrari%"';
        $cars = $manager->executeQuery($phql);

        $phql = 'SELECT * FROM Cars WHERE Cars.name NOT LIKE "Ferrari%"';
        $cars = $manager->executeQuery($phql);

        $phql = 'SELECT * FROM Cars WHERE Cars.price IS NULL';
        $cars = $manager->executeQuery($phql);

        $phql = 'SELECT * FROM Cars WHERE Cars.id IN (120, 121, 122)';
        $cars = $manager->executeQuery($phql);

        $phql = 'SELECT * FROM Cars WHERE Cars.id NOT IN (430, 431)';
        $cars = $manager->executeQuery($phql);

        $phql = 'SELECT * FROM Cars WHERE Cars.id BETWEEN 1 AND 100';
        $cars = $manager->executeQuery($phql);

        $phql = 'SELECT * FROM Cars WHERE Cars.name = :name:';
        $cars = $manager->executeQuery($phql,
            ['name' => 'Lamborghini Espada']
        );

        $phql = 'SELECT * FROM Cars WHERE Cars.name = ?0';
        $cars = $manager->executeQuery($phql,
            [0 => 'Lamborghini Espada']
        );

        // 插入没有列
        $phql = 'INSERT INTO Cars VALUES (NULL, "Lamborghini Espada", 7, 10000.00, 1969, "Grand Tourer")';
        $manager->executeQuery($phql);

        // 指定要插入的列
        $phql = 'INSERT INTO Cars (name, brand_id, year, style) VALUES ("Lamborghini Espada", 7, 1969, "Grand Tourer")';
        $manager->executeQuery($phql);

        // 使用占位符插入
        $phql = 'INSERT INTO Cars (name, brand_id, year, style) VALUES (:name:, :brand_id:, :year:, :style)';
        $manager->executeQuery($phql,
            [
                'name'     => 'Lamborghini Espada',
                'brand_id' => 7,
                'year'     => 1969,
                'style'    => 'Grand Tourer',
            ]
        );


        // 更新单个列
        $phql = 'UPDATE Cars SET price = 15000.00 WHERE id = 101';
        $manager->executeQuery($phql);

        // 更新多个列
        $phql = 'UPDATE Cars SET price = 15000.00, type = "Sedan" WHERE id = 101';
        $manager->executeQuery($phql);

        // 更新多行
        $phql = 'UPDATE Cars SET price = 7000.00, type = "Sedan" WHERE brands_id > 5';
        $manager->executeQuery($phql);

        // 使用占位符
        $phql = 'UPDATE Cars SET price = ?0, type = ?1 WHERE brands_id > ?2';
        $manager->executeQuery($phql,
            [0 => 7000.00, 1 => 'Sedan', 2 => 5]
        );

        // 使用占位符
        $phql = 'DELETE FROM Cars WHERE id BETWEEN :initial: AND :final:';
        $manager->executeQuery($phql,
            ['initial' => 1, 'final'   => 100 ]
        );


        // 获取整个集合
        $robots = $this->modelsManager->createBuilder()
            ->from('Robots')
            ->join('RobotsParts')
            ->orderBy('Robots.name')
            ->getQuery()
            ->execute();

        // 获取第一行
        $robots = $this->modelsManager->createBuilder()
            ->from('Robots')
            ->join('RobotsParts')
            ->orderBy('Robots.name')
            ->getQuery()
            ->getSingleResult();


        // 'SELECT Robots.* FROM Robots';
        $builder->from('Robots');

// 'SELECT Robots.*, RobotsParts.* FROM Robots, RobotsParts';
        $builder->from(
            [
                'Robots',
                'RobotsParts',
            ]
        );

// 'SELECT * FROM Robots';
        $phql = $builder->columns('*')
            ->from('Robots');

// 'SELECT id FROM Robots';
        $builder->columns('id')
            ->from('Robots');

// 'SELECT id, name FROM Robots';
        $builder->columns(['id', 'name'])
            ->from('Robots');

// 'SELECT Robots.* FROM Robots WHERE Robots.name = 'Voltron'';
        $builder->from('Robots')
            ->where("Robots.name = 'Voltron'");

// 'SELECT Robots.* FROM Robots WHERE Robots.id = 100';
        $builder->from('Robots')
            ->where(100);

// 'SELECT Robots.* FROM Robots WHERE Robots.type = 'virtual' AND Robots.id > 50';
        $builder->from('Robots')
            ->where("type = 'virtual'")
            ->andWhere('id > 50');

// 'SELECT Robots.* FROM Robots WHERE Robots.type = 'virtual' OR Robots.id > 50';
        $builder->from('Robots')
            ->where("type = 'virtual'")
            ->orWhere('id > 50');

// 'SELECT Robots.* FROM Robots GROUP BY Robots.name';
        $builder->from('Robots')
            ->groupBy('Robots.name');

// 'SELECT Robots.* FROM Robots GROUP BY Robots.name, Robots.id';
        $builder->from('Robots')
            ->groupBy(['Robots.name', 'Robots.id']);

// 'SELECT Robots.name, SUM(Robots.price) FROM Robots GROUP BY Robots.name';
        $builder->columns(['Robots.name', 'SUM(Robots.price)'])
            ->from('Robots')
            ->groupBy('Robots.name');

// 'SELECT Robots.name, SUM(Robots.price) FROM Robots GROUP BY Robots.name HAVING SUM(Robots.price) > 1000';
        $builder->columns(['Robots.name', 'SUM(Robots.price)'])
            ->from('Robots')
            ->groupBy('Robots.name')
            ->having('SUM(Robots.price) > 1000');

// 'SELECT Robots.* FROM Robots JOIN RobotsParts';
        $builder->from('Robots')
            ->join('RobotsParts');

// 'SELECT Robots.* FROM Robots JOIN RobotsParts AS p';
        $builder->from('Robots')
            ->join('RobotsParts', null, 'p');

// 'SELECT Robots.* FROM Robots JOIN RobotsParts ON Robots.id = RobotsParts.robots_id AS p';
        $builder->from('Robots')
            ->join('RobotsParts', 'Robots.id = RobotsParts.robots_id', 'p');

// 'SELECT Robots.* FROM Robots
// JOIN RobotsParts ON Robots.id = RobotsParts.robots_id AS p
// JOIN Parts ON Parts.id = RobotsParts.parts_id AS t';
        $builder->from('Robots')
            ->join('RobotsParts', 'Robots.id = RobotsParts.robots_id', 'p')
            ->join('Parts', 'Parts.id = RobotsParts.parts_id', 't');

// 'SELECT r.* FROM Robots AS r';
        $builder->addFrom('Robots', 'r');

// 'SELECT Robots.*, p.* FROM Robots, Parts AS p';
        $builder->from('Robots')
            ->addFrom('Parts', 'p');

// 'SELECT r.*, p.* FROM Robots AS r, Parts AS p';
        $builder->from(['r' => 'Robots'])
            ->addFrom('Parts', 'p');

// 'SELECT r.*, p.* FROM Robots AS r, Parts AS p';
        $builder->from(['r' => 'Robots', 'p' => 'Parts']);

// 'SELECT Robots.* FROM Robots LIMIT 10';
        $builder->from('Robots')
            ->limit(10);

// 'SELECT Robots.* FROM Robots LIMIT 10 OFFSET 5';
        $builder->from('Robots')
            ->limit(10, 5);

        $builder->from('Robots')
            ->betweenWhere('id', 1, 100);

        $builder->from('Robots')
            ->inWhere('id', [1, 2, 3]);


        $builder->from('Robots')
            ->notInWhere('id', [1, 2, 3]);

// 'SELECT Robots.* FROM Robots WHERE name LIKE '%Art%';
        $builder->from('Robots')
            ->where('name LIKE :name:', ['name' => '%' . $name . '%']);

// 'SELECT r.* FROM Store\Robots WHERE r.name LIKE '%Art%';
        $builder->from(['r' => 'Store\Robots'])
            ->where('r.name LIKE :name:', ['name' => '%' . $name . '%']);

        // 在查询构造中传递参数
        $robots = $this->modelsManager->createBuilder()
            ->from('Robots')
            ->where('name = :name:', ['name' => $name])
            ->andWhere('type = :type:', ['type' => $type])
            ->getQuery()
            ->execute();

// 在查询执行中传递参数
        $robots = $this->modelsManager->createBuilder()
            ->from('Robots')
            ->where('name = :name:')
            ->andWhere('type = :type:')
            ->getQuery()
            ->execute(['name' => $name, 'type' => $type]);


        \Phalcon\Mvc\Model::setup(['phqlLiterals' => false]);

        //转义保留字
        $phql   = 'SELECT * FROM [Update]';
        $result = $manager->executeQuery($phql);

        $phql   = 'SELECT id, [Like] FROM Posts';
        $result = $manager->executeQuery($phql);


    }

    public function qinduan(){

        // Add some local CSS resources
        $this->assets->addCss('css/style.css');
        $this->assets->addCss('css/index.css');

        // And some local JavaScript resources
        $this->assets->addJs('js/jquery.js');
        $this->assets->addJs('js/bootstrap.min.js');

        //在视图中
        //$this->assets->outputCss(); 或 {{ assets.outputCss() }}
        //$this->assets->outputJs(); 或 {{ assets.outputJs() }}
    }



}
