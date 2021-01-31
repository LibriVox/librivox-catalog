<?php

$config['languages'] = array(
     'english'=>'English',
     'dutch'=>'Nederlands - Dutch',
     'french'=>'Fran&ccedil;ais - French',
     'german'=>'Deutsch - German',
     'italian'=>'Italiano - Italian',
     'portuguese'=>'Portugu&ecirc;s - Portuguese',
     'spanish'=>'Espa&ntilde;ol - Spanish',
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