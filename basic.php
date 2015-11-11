<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['pay_type'] = array(
	'person'	=>	'In Person',
	'card'		=>	'Card',
	'credit'	=>	'Credit',
	'paypal'	=>	'PayPal',
);

$config['user_type'] = array(
	0	=>	'Band',
	2	=>	'Manager',
	1	=>	'CEO',
);

$config['user_type_to_edit'] = array(
	0	=>	'Band',
	2	=>	'Manager',
	1	=>	'CEO',
);

$config['month'] = array(
	1 => 'Jan',
	2 => 'Feb',
	3 => 'Mar',
	4 => 'Apr',
	5 => 'May',
	6 => 'Jun',
	7 => 'Jul',
	8 => 'Aug',
	9 => 'Sep',
	10 => 'Oct',
	11 => 'Nov',
	12 => 'Dec',
);

for ($i=2000;$i<=2050;$i++) {
	$config['year'][$i] = $i;
}