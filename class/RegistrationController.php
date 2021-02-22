<?php


namespace Hostdon;

use Carbon\Carbon;


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
}