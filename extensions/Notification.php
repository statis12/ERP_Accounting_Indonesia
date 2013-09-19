<?php
/*
 *
 * Class : Notification
 * class untuk mengcreate Notification per User Group
 *
 * author: Peter J. Kambey
 * 26 November 2012
 * gratis
 *
 * contoh:
 * echo Notification::create($recepient int,$url string, $message string);
 * 
 * 
 */
Class Notification {
    public function create($group, $url, $message, $company = null, $photopath = "") {
        $model = new sNotification();
        $model->group_id = $group;
        $model->link = $url;
        $model->content = $message;
        if ($company == null && !Yii::app()->user->isGuest) {
            $model->company_id = sUser::model()->myGroup;
        }
        else
            $model->company_id = $company;
        $model->photo_path = $photopath;
        if ($model->save(false)) {
            return true;
        }
        else
            return false;
    }
    public function newInbox($recipient, $subject, $message) {
        $conv = new Mailbox(); //s_mailbox_conversation
        $conv->subject = $subject;
        $conv->initiator_id = 1;
        $conv->interlocutor_id = $recipient;
        $conv->modified = time();
        $conv->bm_read = 0;
        $msg = new Message; //s_mailbox_message
        $msg->text = $message;
        $msg->created = time();
        $msg->sender_id = 1;
        $msg->recipient_id = $recipient;
        $msg->crc64 = 0;
        $conv->save(false);
        $msg->conversation_id = $conv->conversation_id;
        $msg->save(false);
        return true;
    }
    	
    public static function getMessageCount() {
        $criteria = new CDbCriteria;
        $criteria->with=array('conversation');
        //$criteria->together=true;
        $criteria->compare('conversation.bm_read', 1);
        $criteria->compare('recipient_id', Yii::app()->user->id);

        $notifiche = Message::model()->count($criteria);

        //return (int) $notifiche;

		return ((int)$notifiche != 0) ? CHtml::tag("span", array('style' => 'font-size:inherit', 'class' => 'badge badge-info'), $notifiche) : "";

    }

	
}
?>
