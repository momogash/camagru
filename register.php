<?php
    require_once 'core/init.php';
    if(Input::exists())
    {
        $salt = Input::get('token');
        if(Token::check($salt))
        {
            // create an instance object of the class validate
            $validate = new Validate();
            //access the method checkform using the object of the class.
            $validation = $validate->check($_POST, array(
                // contains all the rules for each field we need for validation
                //field name in database should match the 'name'tag on the form
                'username' => array(
                    'required' => true,
                    'name_regex_match' => '/^[a-zA-Z0-9].{6,}+$/',
                    'unique' => 'users'
                ),
                'email' => array(
                    'required' => true,
                    'unique' => 'users',
                    'max' => 50,
                    'valid_email' => true
                ),
                'password' => array(
                    'required' => true,
                    'pass_regex_match' => '/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,}$/',
                ),
                'passwordConf' => array(
                    'required' => true,
                    'matches' => 'password',
                ),
                'name' => array(
                    'required' => true,
                    'min' => 2,
                    'max' => 20
                )
            ));
        //var_dump($validation);
        //die();
            if($validation->passed())
            {
                //since the validation has passed, we now want to create the user with the information given.
                $user = new User();
                $salt = Hash::salt(32); 
                

                try{
                    $user->create(array(
                        'username' => Input::get('username'),
                        'password' => Hash::make(Input::get('password'), $salt),
                        'salt' => $salt,
                        'name' => Input::get('name'),
                        'email' => Input::get('email'),
                        'joined' => date('Y-m-d H:i:s'),
                        'group' => 1,
                        'salt'=> $salt,
                    ));

                    $email = Input::get('email');
                    $username = Input::get('username');
                    $subject = 'Account Registration / Verification';
                    $message = 'Welcome to CAMAGRU!!!';
                    $message .= "\r\n";
                    $message .= 'You are successfully registered, Please select "Log In" to access your account.';
                    $message .= "\r\n";
                    $message .= "<a href='http://localhost:8080/camagru/login.php?user=$username&salt=$salt'>Log In</a>";
                    $headers = 'From:noreply@camagru.com' . "\r\n";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-Type:text/html;charset=UTF-8". "\r\n";
                    mail($email, $subject, $message, $headers); 

                    //Session::flash('home', 'you have been redirected to homepage');
                    //Redirect::to('index.php');

                }
                catch(Exception $e){
                    die($e->getMessage());

                }

                //Redirect::to('index.php');

                
            }
            else
            { 
                foreach($validation->errors() as $error)
                {
                    echo $error, '<br>';
                }  
                
            }
            
    }
   
}
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once './header.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="./CSS/header.css">
    <link rel="stylesheet" type="text/css" href="./CSS/footer.css">
    <link rel="stylesheet" type="text/css" href="./CSS/signup.css">

</head>
<body>
    
    <div >
        <h1> Register Here</h1>
     <!--   <img src="../img/logo.png"><br><br> --->
    <form action="register.php" method="POST">
        <table class="container"><tr>
            <td>username:</td>
            <td><input type="text"  name="username" <?php echo escape(Input::get('username')); ?> ></td><tr></tr>
            <td>Email:</td>
            <td><input type="email" name="email" id="email"></td><tr></tr>
            <td>Name:</td>
            <td><input type="text" name="name" id="name" <?php echo escape(Input::get('name')); ?> ></td><tr></tr>
            <td>Password:</td>
            <td><input type="password" name="password" ></td><tr></tr>
            <td>Confirm Password:</td>
            <td><input type="password" name="passwordConf"></td><tr></tr>
            <td><input type="hidden" name="token" value="<?php echo Token::generate();?>" ></td><tr></tr>
            <td><input type="submit" name="submit" value="Register Now"></td>
        </tr>
        </table>
        <div>
            <p err1 hidden>wrong password</p>
        </div>
    </form>
<?php include_once './footer.php'; ?>
</body>
</html>