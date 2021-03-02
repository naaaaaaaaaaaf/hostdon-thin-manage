<?php


namespace Hostdon;

use Carbon\Carbon;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Valitron\Validator;

class RegistrationController extends Abstracts
{

    public function index(): string
    {
        $_SESSION['token'] = CsrfValidator::generate();
        $temp = self::twig()->load('registration/email.html');
        $messages[] = (new MessageConstructs)->add_message('メールアドレスを入力してください','info');

        return $temp->render(['token' => $_SESSION['token'], 'messages' => $messages]);
    }
    public function email_post(): string
    {


        $messenger = new MessageConstructs();
        $_SESSION['token'] = CsrfValidator::generate();
        //csrfチェック
        if(!CsrfValidator::validate($_POST['token'])){
            exit();
        }
        //アドレスチェック
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $messages[] = $messenger->add_message('無効なメールアドレスです','warning');
            $temp = self::twig()->load('registration/email.html');
            return $temp->render(['token' => $_SESSION['token'], 'messages' => $messages]);
        }
        //既に登録されているか確認
        $db = new DatabaseLoader();
        $is_registration = $db->db()->table('member')->select('*')->where('mail', $_POST['email'])->get()->count();
        if($is_registration > 0){
            $messages[] = $messenger->add_message('そのメールアドレスは既に登録されています','warning');
            $temp = self::twig()->load('registration/email.html');
            return $temp->render(['token' => $_SESSION['token'], 'messages' => $messages]);
        }
        //仮メール送信
        $url_token = hash('sha256', uniqid(rand(), 1));
        $mail_data = sprintf(file_get_contents(__DIR__ . '/../templates/mail/pre_registration.html'),$_ENV['SERVER'].$url_token);
        $mail = new MailController();
        $mail->send_mail('[Hostdon] 仮登録のお知らせ',$mail_data,$_POST['email']);
        //DB操作
        $db->db()->table('pre_member')->insert(['token' => $url_token,'mail'=> $_POST['email'],'date'=>Carbon::now(),'flag' => False]);
        $messages[] = $messenger->add_message('入力されたメールアドレスに仮登録メールを送信しました。添付のURLより本登録を進めてください。','info');
        $temp = self::twig()->load('registration/email.html');
        return $temp->render(['token' => $_SESSION['token'], 'messages' => $messages]);
    }

    public function viewForm($token): string
    {
        session_start();
        $_SESSION['token'] = CsrfValidator::generate();
        $messenger = new MessageConstructs();
        //Tokenチェック
        $db = new DatabaseLoader();
        $result = $db->db()->table('pre_member')->select('*')->where('token','=',$token['token'])->where('flag','=',False)->whereRaw('date > now() - interval 24 hour')->get()->toArray();
        if(!$result) {
            $messages[] = $messenger->add_message('すでに登録済み、若しくは有効期限切れです。最初からやりなおしてください','warning');
            $temp = self::twig()->load('registration/error.html');
            return $temp->render(['messages' => $messages]);
        }

        $_SESSION['mail']=$result[0]->mail;
        //本登録フォーム表示
        $temp = self::twig()->load('registration/form/form.html');
        return $temp->render(['token' => $_SESSION['token'],'stripe_public_key'=>$_ENV['STRIPE_PUBLIC_KEY']]);
    }


    public function registration(): string
    {
        session_start();
        if(!CsrfValidator::validate($_POST['token'])){
            exit();
        }
        //todo: tokenチェックどうしよ hiddenPOST?? GET取れるのか？


        //バリデーション
        $v = new Validator($_POST);
        $v->rule('required', ['password','name','zip01','pref01','addr01','addr02','stripeToken'])->message('{field}は必須です。');
        $v->rule('ascii', 'password')->message('正しい{field}の書式で入力してください。');
        $v->rule('lengthBetween', 'password',8,256)->message('{field}は8文字以上256文字以下で入力してください。');
        $v->rule('lengthMax','name',30)->message('{field}は30文字以下で入力してください');
        $v->rule('length','zip01',7)->message('{field}は7文字で入力してください。');
        $v->rule('numeric','zip01')->message('{field}は数字で入力してください。');
        $v->rule('lengthMax','pref01',10)->message('{field}は10文字以下で入力してください。');
        $v->rule('lengthMax','addr01',30)->message('{field}は30文字以下で入力してください。');
        $v->rule('lengthMax','addr02',50)->message('{field}は50文字以下で入力してください。');
        $v->labels(array(
            'password' => 'パスワード',
            'name' => 'お名前',
            'zip01' => '郵便番号',
            'pref01' => '都道府県',
            'addr01' => '住所(市区町村)',
            'addr02' => '住所(番地以降)',
            'stripeToken' => 'クレジットカード情報'
        ));
        if (!$v->validate()) {
            $messages = $v->errors();
            $temp = self::twig()->load('registration/form/form.html');
            $_SESSION['token'] = CsrfValidator::generate();
            return $temp->render(['token' => $_SESSION['token'] ,'messages' => $messages,'stripe_public_key'=>$_ENV['STRIPE_PUBLIC_KEY']]);
        }else{
            //本登録
            try {
                $stripe = new StripeClient(
                    $_ENV['STRIPE_SECRET_KEY']
                );
                $cu = $stripe->customers->create([
                    'email' => $_SESSION['mail'],
                    'source' => $_POST['stripeToken']
                ]);
                $stripe->customers->update(
                    $cu['id'],
                    ['address' => ['city' => $_POST['addr01'],'country'=>'JP','line1'=>$_POST['addr02'],'postal_code'=>$_POST['zip01'],'state'=>$_POST['pref01']],'name'=>$_POST['name'] ]
                );

            } catch (ApiErrorException) {
                $temp = self::twig()->load('registration/form/error.html');
                return $temp->render();
            }
            //DB
            $db = new DatabaseLoader();
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $db->db()->table('member')->insert(['mail' => $_SESSION['mail'],'password'=>$password,'cus_id' => $cu['id']]);
            $db->db()->table('pre_member')->where('mail','=',$_SESSION['mail'])->update(['flag' => True]);

            //todo: メール送れ

            $temp = self::twig()->load('registration/form/complete.html');
            return $temp->render();
        }
    }

}