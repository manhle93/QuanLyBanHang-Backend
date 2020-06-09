Thay thế tài khoản firebase

Trong forder Observers, file DiemChayObserver

Thay thế code cũ bằng đoạn code sau:

              $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/../../firebaseBackup.json'); // đường dẫn của file json ta vừa tải phía trên
            $firebase           = (new Factory)
               ->withServiceAccount($serviceAccount)
                 ->withDatabaseUri('https://pccc-2187f.firebaseio.com') //bạn có thẻ lấy project id ở mục project setting > general
                ->create();
            $database = $firebase->getDatabase();

             $userRepository = $database->getReference('diem_chay'); //lấy model .
             $userRepository->push($model);
