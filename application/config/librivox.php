<?php

$config['languages'] = array(
	'english'    => 'English',
	'zh_cn'      => '汉语 - Chinese',
	'dutch'      =>' Nederlands - Dutch',
	'french'     => 'Français - French',
	'german'     => 'Deutsch - German',
	'italian'    => 'Italiano - Italian',
	'portuguese' => 'Português - Portuguese',
	'russian'    => 'Русский - Russian',
	'spanish'    => 'Español - Spanish',
	'ukrainian'  => 'Українська - Ukrainian',
);

$config['title_remove'] = array(
	"The","An","A",
	"Een","De","Het",
	"Le","La","Les","Un","Une","Des",
	"Der","Die","Das","Ein","Eine",
	"El","Los","Las",
	"O","Os","A","As","Um","Uns","Uma","Umas",
);


$config['project_statuses'] = array(
     '' => '-- Select status --',
     'open'=>'Open', 
     'fully_subscribed' => 'Fully Subscribed',
     'proof_listening' => 'Proof Listening',
     'validation' => 'Validation',
     'complete'=>'Complete',
     'abandoned' => 'Abandoned',
     'on_hold' => 'On Hold'
);


$config['roles'] = array(
PERMISSIONS_READERS=>  3,
PERMISSIONS_MEMBERS=>  2,
PERMISSIONS_ADMIN=>    1,
PERMISSIONS_BCS=>      5,
PERMISSIONS_MCS=>      4,
PERMISSIONS_UPLOADER=> 6,
PERMISSIONS_PLS=>      7,

);
