<?php

App::uses('UsersAppModel', 'Users.Model'); 

/**
 * User
 *
 * @category Model
 * @package  Croogo.Users.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Lesson extends UsersAppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Lesson';

    public $id;
    public $user_id_to_message;

    public $vistor_id;
 

    public function add($data)
    {
        // this gets run when a student proposes a lesson to a tutor
        if (isset($data['Lesson']['tutor']) && $data['Lesson']['tutor'] != "") {

            $this->user_id_to_message = (int)$data['Lesson']['tutor'];

            $data['Lesson']['tutor'] = $this->user_id_to_message;
            $data['Lesson']['created'] = $this->vistor_id;
            $data['Lesson']['add_date'] = date('Y-m-d');
            $data['Lesson']['readlesson'] = '1';
            $data['Lesson']['readlessontutor'] = '0';
            $data['Lesson']['is_confirmed'] = '0';
            $data['Lesson']['laststatus_tutor'] = 0;
            $data['Lesson']['laststatus_student'] = 1;

            // @TODO: decide whether we want to track whether this lesson's user has been vetted as a billable Customer here or not
            // seems like it might be better to check that right against the Users table with a join ...

            $user = ClassRegistry::init(array('class' => 'Users.User', 'alias' => 'User'));
            $student = $user->find('first', array('conditions' => array('User.id' => $this->vistor_id)));

            // if we don't have a stripe customer id for this student, then we need billing info
            $needsBillingInfo = (!isset($student['User']['stripe_customer_id'])
                || $student['User']['stripe_customer_id'] == "")
                ? true
                : false;

        } else {
            // this gets run when a tutor creates a lesson to do with a student on the /users/createlessons page
            $user = ClassRegistry::init(array('class' => 'Users.User', 'alias' => 'User'));
            $tutorid = $user->find('first', array('conditions' => array('username' => $data['Lesson']['tutorname'])));
            $tutorid = $tutorid['User']['id'];

            // we'll want to message this person below
            $this->user_id_to_message = $tutorid;

            $data['Lesson']['tutor'] = $this->vistor_id;
            $data['Lesson']['created'] = $tutorid;
            $data['Lesson']['add_date'] = date('Y-m-d');
            $data['Lesson']['readlesson'] = '0';
            $data['Lesson']['readlessontutor'] = '1';
            $data['Lesson']['is_confirmed'] = '0';
            $data['Lesson']['laststatus_tutor'] = 1;
            $data['Lesson']['laststatus_student'] = 0;
        }

        if ($this->save($data, false)) {
            $this->id = $this->getLastInsertId();
            return true;
        } else {
            return false;
        }
    }


/**
 * Validation
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'tutor' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field cannot be left blank.',
				'last' => true,
			),
		),
		'lesson_date' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field cannot be left blank.',
				'last' => true,
			),
		),
		'lesson_time' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field cannot be left blank.',
				'last' => true,
			),
		),
		'duration' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field cannot be left blank.',
				'last' => true,
			),
		),
		'subject' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field cannot be left blank.',
				'last' => true,
			)
		),
// @TODO: this was breaking
//		'repet' => array(
//			'notEmpty' => array(
//				'rule' => 'notEmpty',
//				'message' => 'This field cannot be left blank.',
//				'last' => true,
//			),
//		),
		'notes' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field cannot be left blank.',
				'last' => true,
			),
		),
	);
    

}
