<?php
    include("connect.php");
    require "PHPMailer/src/PHPMailer.php"; 
    require "PHPMailer/src/SMTP.php"; 
    require 'PHPMailer/src/Exception.php'; 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // error_reporting(1);
    
    class dataProcessor extends database {

        // Select data
        public function selectData($table, $whereCondition) {
            $array = [];
            $condition = "";
            foreach($whereCondition as $key => $value) {
                $condition .= $key . " = '" . $value . "' AND ";
                // echo "$key ";
            }
            $condition = substr($condition, 0, -4);
            $query = "SELECT * FROM ".$table." WHERE " . $condition;
            $result = mysqli_query($this->_connection, $query);
            if(!$result){
                return NULL;
            }
            while($row = mysqli_fetch_assoc($result)) {
                $array[] = $row;
            }
            mysqli_free_result($result);
            
            return $array;
        }

        // Select a table
        public function selectTable($table) {
            $array = [];
            $query = "SELECT * FROM ".$table."";
            $result = mysqli_query($this->_connection, $query);
            if(!$result){
                return NULL;
            }
            while($row = mysqli_fetch_assoc($result)) {
                $array[] = $row;
            }
            mysqli_free_result($result);
            return $array;
        }


        // Insert data
        public function insert($table, $data) {
            $query = "INSERT INTO ".$table." (";
            $query .= implode(",", array_keys($data)) .') VALUES (';
            $query .= "'" . implode("','", array_values($data)) ."');";
            // echo "$query";
            return $query;
        }

        

        // Update data
        public function update($table, $data, $whereCondition) {
            $dataUpdate = '';
            $condition = "";

            foreach($data as $key => $value) {
                $dataUpdate .= $key . " =' " . $value . "', ";
            }
            $dataUpdate = substr($dataUpdate, 0, -2);
            foreach($whereCondition as $key => $value) {
                $condition .= $key . " = '" . $value . "' AND ";
            }
            $condition = substr($condition, 0, -4);
            $query = "UPDATE " . $table . "SET " . $dataUpdate . "WHERE" . $condition . "";
            $result = mysqli_query($this->_connection, $query);
            if(!$result){
                return NULL;
            }
            return true;
        }

        // Delete data
        public function delete($table, $whereCondition) {
            $condition = "";
            foreach($whereCondition as $key => $value) {
                $condition .= $key . " = '" . $value . "' AND ";
            }
            $condition = substr($condition, 0, -4);
            $query = "DELETE FROM ". $table ." WHERE ". $condition ."";
            $result = mysqli_query($this->_connection, $query);
            if(!$result){
                return NULL;
            }
            return true;
        }

        public function selectDataPagination($table, $page, $limit) {
            $array = [];
            $start = ($page -1) * $limit;
            $query = "SELECT * FROM ".$table." ORDER BY DESC LIMIT ".$start." ',' ".$limit."";
            $result = mysqli_query($this->_connection, $query);
            if(!$result){
                return NULL;
            }
            while($row = mysqli_fetch_assoc($result)) {
                $array[] = $row;
            }
            mysqli_free_result($result);
            if(!empty($array)){
                return $array;
            }
            return false;
        }

        public function countPage($table, $page, $limit) {
            $query = "SELECT COUNT(id) AS id FROM ".$table."";
            $result = mysqli_query($this->_connection, $query);
            if(!$result){
                return NULL;
            }
            while($row = mysqli_fetch_assoc($result)) {
                $total = $row[0]['id'];
            }
            mysqli_free_result($result);
            $pages = ceil($total / $limit);
            return $pages;
            
        }

        function runArray($array) {
            if(!empty($array)){
                foreach ($array as $key){
                    return $key;
                }
            }
        }

        

        public function addComment($idPost, comment $comment) {
            $sql = $this->insert("comments", $comment->castToArray());
            $this->setQuery($sql);
            $this->query();
        }


        public function renderComment($idPost) {
            $listComment = $this->selectData("comments", $idPost);
            if($listComment <> 0) {
                foreach ($listComment as $comment){
                    echo'   
                    <div class="--c--comment--content">
                        <div class="c__user_flexcoment">
                            <p>

                                <i class="fa fa-user-circle" aria-hidden="true"></i>

                            </p>
                            <div class="c__user_coment">
                                <p class="c__user_coment" name="nameuser">'. $this->runArray($this->selectData("users", ['idUser' => $comment['idUser']]))['username'] .'</p>

                                <p class="--c--conten--date" name="date">'. $comment['dateComment'] .'</p>
                                <p class="--c--content--detail" name="content-user">'. $comment['content'] .'</p>

                            </div>
                        </div>
                    </div>
                    ';
                }
            } else {
                echo "Nothing to show";   
            }   
        }  

        public function checkLike($condition) {
            
            $result = $this->selectData("likes", $condition);
            $count = count($result);
            if($count == 0) {
                return false;
            }
            return true;
        }

        public function addLikePost(like $like) {
            $sql = $this->insert("likes", $like->castToArray());
            $this->setQuery($sql);
            $this->query();
        }

        public function addWishList(wishList $wishList) {
            $sql = $this->insert("wishlist", $wishList->castToArray());
            $this->setQuery($sql);
            $this->query();
        }

        public function checkWishList($condition) {
            
            $result = $this->selectData("wishlist", $condition);
            $count = count($result);
            if($count == 0) {
                return false;
            }
            return true;
        }
        public function checkLogin($email, $password){
            
            $email = $_POST['email'];
            $password = $_POST['password'];
            $condition = [
                'email' => $email,
                'pass' => $password
            ];
            $result= $this->selectData('Users', $condition);
            if(!empty($result)){
                return true;
            }
            return false;
        }

        // public function sendEmail($email,$newpass){
        //     $mail = new PHPMailer(true);//true:enables exceptions
        //     try {
        //         $mail->SMTPDebug = 0; //0,1,2: ch??? ????? debug
        //         $mail->isSMTP();  
        //         $mail->CharSet  = "utf-8";
        //         $mail->Host = 'smtp.gmail.com';  //SMTP servers
        //         $mail->SMTPAuth = true; // Enable authentication
        //         $mail->Username = 'dennttinh@gmail.com'; // SMTP username
        //         $mail->Password = 'hothiden25102002';   // SMTP password
        //         $mail->SMTPSecure = 'ssl';  // encryption TLS/SSL 
        //         $mail->Port = 465;  // port to connect to                
        //         $mail->setFrom('dennttinh@gmail.com', 'Ben Quik website' ); 
        //         $mail->addAddress($email); 
        //         $mail->isHTML(true);  // Set email format to HTML
        //         $mail->Subject = 'Th?? g???i l???i m???t kh???u';
        //         $noidungthu = "<p>????y l?? m???t kh???u m???i '.$newpass'</p>"; 
        //         $mail->Body = $noidungthu;
        //         $mail->smtpConnect( array(
        //             "ssl" => array(
        //                 "verify_peer" => false,
        //                 "verify_peer_name" => false,
        //                 "allow_self_signed" => true
        //             )
        //         ));
        //         $mail->send();
        //         echo '???? g???i mail xong';
        //     } catch (Exception $e) {
        //         echo 'Error: ', $mail->ErrorInfo;
        //     }
        // }

        // public function processSendEmail(){
        //     $email = $_POST['email'];
		//     $password = $_POST['password'];
		//     $sql = "SELECT * FROM Users WHERE email = '$email'";
        //     $result= mysqli_query($this->_connection,$sql);
        //     $resu = mysqli_fetch_assoc($result);

        //     if (mysqli_num_rows($result) ==0) {
        //         echo'ch??a ????ng k?? t??i kho???n';
        //     }
        //     else{
        //         $this->sendEmail($email,$password);
        //         $sqlUpdate = "UPDATE `Users` SET `pass`= '". $password ."' WHERE `userID` = ". $resu['userID'];
        //         mysqli_query($this->_connection,$sqlUpdate);
        //         //echo " l???i ???? c???p nh???t";
        //     }
            
        // }

        public function checkAvailableEmail($email) {
            $condition = ['email' => $email];
            $result =  $this->selectData("user", $condition);
            if(!empty($result)){
                return true;
            }
            return false;
        }

        public function checkConfirmPassword($password, $confirmPassword) {
            if($password == $confirmPassword) {
                return true;
            }
            return false;
        }

        public function registration(user $user) {
            $sql = $this->insert("user", $user->castToArray());
            $this->setQuery($sql);
            $this->query();
        }

        public function processSendEmail($email) {
            //$email=$_POST['email'];
            $condition = [
                'email' => $email,
            ];

            $result = $this->selectData("user", $condition);
            if (!empty($result)) {
                $this->sendEmail($email);
                echo "<script>alert('You got an email');</script>";
            }
            else{
                echo "<script>alert('You need have an account');</script>";
    
            }
            
        }

        public function sendEmail($email){
            $username=$_POST['username'];
            $password=$_POST['password'];
          
    
            $mail = new PHPMailer(true);//true:enables exceptions
                try {
                    $mail->SMTPDebug = 0; //0,1,2: ch??? ????? debug
                    $mail->isSMTP();  
                    $mail->CharSet  = "utf-8";
                    $mail->Host = 'smtp.gmail.com';  //SMTP servers
                    $mail->SMTPAuth = true; // Enable authentication
                    $mail->Username = 'linh.nguyenthikhanh02@gmail.com'; // SMTP username
                    $mail->Password = 'Khanhlinh112002.';   // SMTP password
                    $mail->SMTPSecure = 'ssl';  // encryption TLS/SSL 
                    $mail->Port = 465;  // port to connect to                
                    $mail->setFrom('linh.nguyenthikhanh02@gmail.com', 'Du l???t c??ng cu p??' ); 
                    $mail->addAddress($email); 
                    $mail->isHTML(true);  // Set email format to HTML
                    $mail->Subject = 'Verify your email';
                    $mail->Body =  "<p>Hello .$email</p><br>
                    <p>M??nh th???y b???n [username: .$username] c?? h???ng th?? v???i vi???c ????ng xu???t kh???i tr??i ?????t c??ng Kh??nh Linh</p>
                    <p style='color:red;'>Ch??c b???n c?? m???t chi???n du l???t vui v??? *???? ????t*</p>
                    <p style='color:red;'>????y l?? m???t kh???u t??i kho???n \".$password\" ?????ng quy??n m???t kh???u ????? ???????c ???? ????t nh??u l???n nh??!</p>
                    <button  style='color:yellow; background:black;'>Tin nh???n ???????c g???i t??? cu p?? ????ng iu nh???t h??? m???t tr???i</button>";
                    //$mail->addAttachment('https://images.pexels.com/photos/1275393/pexels-photo-1275393.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'khanhlinh');
                    // <p>You're receiving this email because you recently signed up for a Ben Quick account. 
                    // To complete the signup process, hit the button below to verify your account.</p><br>
                    // <a href='signin.php'><button>Verify email</button></a><br>
                    // <p>If you didn't sign up for an account with us, please ignore this message :)</p>
                    // <span>- Ben Quick Team</span>";
                    $mail->smtpConnect( array(
                        "ssl" => array(
                            "verify_peer" => false,
                            "verify_peer_name" => false,
                            "allow_self_signed" => true
                        )
                    ));
                    $mail->send();
                } catch (Exception $e) {
                    echo 'Error: ', $mail->ErrorInfo;
                }
        }
            
    }
    

    $test = new dataProcessor;

    // $test->renderData();
    // $test->renderComment(['idPhoto' => 1]);

?>