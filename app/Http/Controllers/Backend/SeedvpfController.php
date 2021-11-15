<?php
namespace App\Http\Controllers\Backend;

use DB;
use App\Http\Controllers\Controller;


class SeedvpfController extends Controller
{
    private $args;
  
    public function __construct(array $args){ //public function __construct(Array args){

        //$db = DB::statement("DROP VIEW IF EXISTS pos_alertstock");
        $db = DB::statement("create or replace view pos_alertstock AS".
            "
            select 
            pos_stockadds.pd_id as pd_id,
            JSON_UNQUOTE(product.title->'$.en') AS product,
            JSON_UNQUOTE(cms_unit.title->'$.en') AS unit,
            product.unt_id as unt_id,
            
            CAST(CONCAT(
                '[',
                GROUP_CONCAT(
                   qty_inhand
                ),
                ']'
            ) AS JSON) AS qty,
            SUM(qtytotal_inhand) as qty_total,
            SUM(cost * qtytotal_inhand)as cost,
            SUM(pcost * qtytotal_inhand) as pcost,
            xtracost,
            wh_id,
            c_id,
            product.tag->'$.stock_alert' as stock_alert,
            product.tag->'$.stocksc_alert' as stocksc_alert
            from `pos_stockadds` inner join `pos_stockadd` on `pos_stockadd`.`asm_id` = `pos_stockadds`.`asm_id` left join `cms_product` as `product` on `product`.`pd_id` = `pos_stockadds`.`pd_id` left join `cms_unit` on `cms_unit`.`unt_id` = `product`.`unt_id` 
            where `avgcost` = 1 and `udcs` = 'yes' and  `product`.`trash` != 'yes' and  `pos_stockadd`.`trash` != 'yes' 
            group by `pos_stockadds`.`pd_id`
            having sum(qtytotal_inhand)>0
                        
           "
        );
      
      $db=DB::statement(
          "create or replace view pos_notification AS".
        "
          select (select count(*) from pos_alertstock) as alertstock;
      
      ");
      //////////////////////////
      
      $db=DB::statement(
          "CREATE TABLE IF NOT EXISTS `pos_quotation` (
  `qt_id` int(11) NOT NULL,
  `yearid` int(11) NOT NULL,
  `vat_id` int(11) NOT NULL,
  `cm_id` int(11) NOT NULL COMMENT 'customer',
  `branch_id` int(11) NOT NULL,
  `title` varchar(300) CHARACTER SET utf8 NOT NULL,
  `stage` int(11) NOT NULL,
  `iss_date` date NOT NULL,
  `due_date` date NOT NULL,
  `fter_note` text CHARACTER SET utf8 NOT NULL,
  `prv_note` tinytext CHARACTER SET utf8 NOT NULL,
  `sale_id` int(11) NOT NULL,
  `mainvat` float NOT NULL,
  `maindiscount` float NOT NULL,
  `inv_cycle` varchar(10) CHARACTER SET utf8 NOT NULL,
  `trash` varchar(10) CHARACTER SET utf8 NOT NULL,
  `tags` text CHARACTER SET utf8 NOT NULL,
  `xch_rate` tinytext CHARACTER SET utf8 NOT NULL,
  `gtotal` float NOT NULL,
  `add_date` date NOT NULL,
  `blongto` int(11) NOT NULL,
  PRIMARY KEY (`qt_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
      
    $db=DB::statement("ALTER TABLE `pos_quotation` MODIFY `qt_id` int(11) NOT NULL AUTO_INCREMENT;");
      
      ///////////////////////////
      
      $db=DB::statement(
          "CREATE TABLE IF NOT EXISTS `pos_quotations` (
  `qtd_id` int(11) NOT NULL,
  `qt_id` int(11) NOT NULL,
  `pd_id` int(11) NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `size_color` varchar(100) CHARACTER SET utf16 NOT NULL,
  `subqty` tinytext CHARACTER SET utf8 NOT NULL,
  `subunit` varchar(100) CHARACTER SET utf8 NOT NULL,
  `unitprice` float NOT NULL,
  `subdiscount` float NOT NULL,
  `subvat` float NOT NULL,
  `cycle` varchar(10) CHARACTER SET utf8 NOT NULL,
  `ordering` int(11) NOT NULL,
  `amount` float NOT NULL,
  `subnote` text CHARACTER SET utf8 NOT NULL,
  `costdetail` text CHARACTER SET utf8 NOT NULL,
  `cost` float NOT NULL,
  PRIMARY KEY (`qtd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
      
    $db=DB::statement("ALTER TABLE `pos_quotations` MODIFY `qtd_id` int(11) NOT NULL AUTO_INCREMENT;");
      
      ///////////////////////////
      
      
      //////////////////////////
      
      $db=DB::statement(
          "CREATE TABLE IF NOT EXISTS `pos_productreturn` (
  `pr_id` int(11) NOT NULL,
  `yearid` int(11) NOT NULL,
  `vat_id` int(11) NOT NULL,
  `cm_id` int(11) NOT NULL COMMENT 'customer',
  `branch_id` int(11) NOT NULL,
  `title` varchar(300) CHARACTER SET utf8 NOT NULL,
  `stage` int(11) NOT NULL,
  `iss_date` date NOT NULL,
  `due_date` date NOT NULL,
  `fter_note` text CHARACTER SET utf8 NOT NULL,
  `prv_note` tinytext CHARACTER SET utf8 NOT NULL,
  `sale_id` int(11) NOT NULL,
  `mainvat` float NOT NULL,
  `maindiscount` float NOT NULL,
  `inv_cycle` varchar(10) CHARACTER SET utf8 NOT NULL,
  `trash` varchar(10) CHARACTER SET utf8 NOT NULL,
  `tags` text CHARACTER SET utf8 NOT NULL,
  `xch_rate` tinytext CHARACTER SET utf8 NOT NULL,
  `gtotal` float NOT NULL,
  `add_date` date NOT NULL,
  `blongto` int(11) NOT NULL,
  PRIMARY KEY (`pr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
      
    $db=DB::statement("ALTER TABLE `pos_productreturn` MODIFY `pr_id` int(11) NOT NULL AUTO_INCREMENT;");
      
      ///////////////////////////
      
      
      $db=DB::statement(
          "CREATE TABLE IF NOT EXISTS `pos_productreturns` (
  `prd_id` int(11) NOT NULL,
  `pr_id` int(11) NOT NULL,
  `pd_id` int(11) NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `size_color` varchar(100) CHARACTER SET utf16 NOT NULL,
  `subqty` tinytext CHARACTER SET utf8 NOT NULL,
  `subunit` varchar(100) CHARACTER SET utf8 NOT NULL,
  `unitprice` float NOT NULL,
  `subdiscount` float NOT NULL,
  `subvat` float NOT NULL,
  `cycle` varchar(10) CHARACTER SET utf8 NOT NULL,
  `ordering` int(11) NOT NULL,
  `amount` float NOT NULL,
  `subnote` text CHARACTER SET utf8 NOT NULL,
  `costdetail` text CHARACTER SET utf8 NOT NULL,
  `cost` float NOT NULL,
  PRIMARY KEY (`prd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
      
    $db=DB::statement("ALTER TABLE `pos_productreturns` MODIFY `prd_id` int(11) NOT NULL AUTO_INCREMENT;");
      
      
      //////////////////////////
      
      $db=DB::statement(
   "CREATE TABLE IF NOT EXISTS `pos_qtstatus` (
  `qts_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `percentage` float NOT NULL,
  `tag` tinytext NOT NULL,
  `trash` varchar(10) NOT NULL,
  `blongto` int(11) NOT NULL,
  PRIMARY KEY (`qts_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
      
    $db=DB::statement("ALTER TABLE `pos_qtstatus` MODIFY `qts_id` int(11) NOT NULL AUTO_INCREMENT;");
      
      ///////////////////////////
      
      //////////////////////////
      
      $db=DB::statement(
   "CREATE TABLE IF NOT EXISTS `pos_purchase` (
  `pch_id` int(11) NOT NULL,
  `yearid` int(11) NOT NULL,
  `vat_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL COMMENT 'Supplier',
  `branch_id` int(11) NOT NULL,
  `title` varchar(300) CHARACTER SET utf8 NOT NULL,
  `stage` int(11) NOT NULL,
  `inv_date` date NOT NULL,
  `due_date` date NOT NULL,
  `fter_note` text CHARACTER SET utf8 NOT NULL,
  `prv_note` tinytext CHARACTER SET utf8 NOT NULL,
  `sale_id` int(11) NOT NULL,
  `mainvat` float NOT NULL,
  `maindiscount` float NOT NULL,
  `inv_cycle` varchar(10) CHARACTER SET utf8 NOT NULL,
  `po_id` int(11) NOT NULL,
  `trash` varchar(10) CHARACTER SET utf8 NOT NULL,
  `tags` text CHARACTER SET utf8 NOT NULL,
  `xch_rate` tinytext CHARACTER SET utf8 NOT NULL,
  `gtotal` float NOT NULL,
  `paid` float NOT NULL,
  `add_date` date NOT NULL,
  `blongto` int(11) NOT NULL,
  PRIMARY KEY (`pch_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
      
    $db=DB::statement("ALTER TABLE `pos_purchase` MODIFY `pch_id` int(11) NOT NULL AUTO_INCREMENT;");
      
      ///////////////////////////
      
      
      $db=DB::statement(
   "CREATE TABLE IF NOT EXISTS `pos_purchases` (
  `pchd_id` int(11) NOT NULL,
  `pch_id` int(11) NOT NULL,
  `pd_id` int(11) NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `size_color` varchar(100) CHARACTER SET utf16 NOT NULL,
  `subqty` tinytext CHARACTER SET utf8 NOT NULL,
  `subunit` varchar(100) CHARACTER SET utf8 NOT NULL,
  `unitprice` float NOT NULL,
  `subdiscount` float NOT NULL,
  `subvat` float NOT NULL,
  `cycle` varchar(10) CHARACTER SET utf8 NOT NULL,
  `ordering` int(11) NOT NULL,
  `amount` float NOT NULL,
  `subnote` text CHARACTER SET utf8 NOT NULL,
  `costdetail` text CHARACTER SET utf8 NOT NULL,
  `cost` float NOT NULL,
  PRIMARY KEY (`pchd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
      
    $db=DB::statement("ALTER TABLE `pos_purchases` MODIFY `pchd_id` int(11) NOT NULL AUTO_INCREMENT;");
      
      ///////////////////////////
      
      
       $db=DB::statement(
   "CREATE TABLE IF NOT EXISTS `pos_ppayment` (
  `pp_id` int(11) NOT NULL,
  `pch_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `pay_date` date NOT NULL,
  `pay_amount` float NOT NULL,
  `discount` float NOT NULL,
  `receipt_no` varchar(200) CHARACTER SET latin1 NOT NULL,
  `tra_fee` float NOT NULL,
  `pmethod_id` int(11) NOT NULL,
  `accno_id` int(11) NOT NULL,
  `ccy_id` int(11) NOT NULL,
  `xchrate` float NOT NULL,
  `tnote` text NOT NULL,
  `rc_by` int(11) NOT NULL,
  `approved_by` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `add_date` date NOT NULL,
  `trash` varchar(10) NOT NULL,
  `blongto` int(11) NOT NULL,
  PRIMARY KEY (`pp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
      
    $db=DB::statement("ALTER TABLE `pos_ppayment` MODIFY `pp_id` int(11) NOT NULL AUTO_INCREMENT;");
      
      ///////////////////////////
      
      $db=DB::statement(
   "CREATE TABLE IF NOT EXISTS `pos_deliverynote` (
  `dlnote_id` int(11) NOT NULL,
  `inv_id` int(11) NOT NULL,
  `dlperson_id` int(11) NOT NULL,
  `iss_date` date NOT NULL,
  `note` text CHARACTER SET utf8 NOT NULL,
  `add_date` date NOT NULL,
  `trash` varchar(10) NOT NULL,
  `blongto` int(11) NOT NULL,
  PRIMARY KEY (`dlnote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
      
    $db=DB::statement("ALTER TABLE `pos_deliverynote` MODIFY `dlnote_id` int(11) NOT NULL AUTO_INCREMENT;");
      
      ///////////////////////////
      
      /*
      
      ALTER TABLE `pos_stockadds` ADD `batch` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `add_date`, ADD `product_expdate` DATE NULL DEFAULT NULL AFTER `batch`, ADD `tags` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `product_expdate`;
      
      ALTER TABLE `pos_customer` ADD `location_id` INT NOT NULL AFTER `generalcustomer`;
      */
      
//       $db = DB::statement(str_replace('\n', '',
//         "DELIMITER $$
// --
// -- Functions
// --
// CREATE FUNCTION `lowstocksc` (`jsonfield` JSON, `chklower` FLOAT) RETURNS TINYINT(1) NO SQL
// BEGIN
//   DECLARE result BOOLEAN;
//   DECLARE i INT;
//   DECLARE numrecord INT;
//   DECLARE child JSON;
  
//   DECLARE countchild int;
//   DECLARE ind int;
//   DECLARE item JSON;
//   DECLARE qty float;
  
//   SET result = false;
//   SET i =0;
//   SET numrecord = JSON_LENGTH(jsonfield);
 	
  
  
//   whileloop:WHILE (i<numrecord) DO
//   	SET child = json_extract(jsonfield, CONCAT('$[',i,']'));
//     SET countchild = JSON_LENGTH(child);
//     IF countchild>0 THEN
//     	SET ind = 0;
//         SET item = json_extract(child,'$.*');
//     	WHILE (ind<countchild) DO
//         	SET qty = json_extract(item,CONCAT('$[',i,']'));
            
//             IF qty<=chklower AND chklower>0 THEN
//             	SET result = true;
//             	LEAVE whileloop;
//             END IF;
        	
//         	SET ind = ind + 1;
//         END WHILE;
    	
    	
//     END IF;
    
  	
//     SET i=i+1;
//   END WHILE whileloop;
  

//   RETURN result;
// END$$

// DELIMITER ;"
//       ));
      
      
      
      
      
      
      
      
      
      

    } 
    /*../function..*/
  
    public function index()
    {
      
      
    }
    
      


    


    
}