<?php
include('smtp/PHPMailerAutoload.php');

$receiverEmail= $_SESSION['email'];
$subject="Slot Booked";
$userName = explode(" ", $_SESSION['name'])[0];
$emailbody = "Hello {$userName},<br>Parking slot for your <b>{$_SESSION['vehicle_number']} {$_SESSION['vehicle_type']}</b> has been reserved.<br> Booking Period: <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;From: {$_SESSION['start_time']}<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {$_SESSION['end_time']}<br> (Booked At: {$_SESSION['booked_at']})<br>Save your reference number: <b>{$_SESSION['ref_number']}</b>.<br><br>Thank You.";
 echo smtp_mailer($receiverEmail,$subject,$emailbody);
 
function smtp_mailer($to,$subject, $msg){
	$mail = new PHPMailer(); 
	$mail->IsSMTP(); 
	$mail->SMTPAuth = true; 
	$mail->SMTPSecure = 'tls'; 
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 587; 
	$mail->IsHTML(true);
	$mail->CharSet = 'UTF-8';
	//$mail->SMTPDebug = 2; 
	$mail->Username = "bookingonlinefromme@gmail.com"; //write sender email address
	$mail->Password = "xxxx xxxx xxxx xxxx"; //write app password of sender email
	$mail->SetFrom("bookingonlinefromme@gmail.com"); //write sender email address
	$mail->Subject = $subject;
	$mail->Body =$msg;
	$mail->AddAddress($to);
	$mail->SMTPOptions=array('ssl'=>array(
		'verify_peer'=>false,
		'verify_peer_name'=>false,
		'allow_self_signed'=>false
	));
	if(!$mail->Send()){
		
	}else{
	
	}
}
?>