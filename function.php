<?php
/**
 * fucntion file all function
 * **/

/*
 * Order Array Get
 * */

if ( ! function_exists( 'shpt_get_orders' ) ) {
function shpt_get_orders()
{
	if(isset($_GET['action']) && isset($_GET['ids']) && $_GET['action']=="bulk_shipping_print" )
	{
		$ids=explode(',',$_GET['ids']);

		if(is_array($ids))
		{


			return $ids;
		}else
		{
			$ids=array();
			return $ids;
		}

	}

}
}
