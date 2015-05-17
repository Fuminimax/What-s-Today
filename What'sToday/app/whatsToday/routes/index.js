var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/', function(req, res, next) {

/*
  var mysql = require('mysql');
  var db_conn = mysql.createConnection({
	host: 'xxxxxxx',
	database: 'xxxxxxx',
	user: 'root',
	password: 'xxxxxxx'
  });

  var sql_sentence = 'select* from xxxxx ......';
  var db_query = db_conn.query(sql_sentence, function(err, db_res) {
                            
                            ;

  });
*/
  var whatsDay_msg = "【What's day?】5月13日（ごがつじゅうさんにち）は、グレゴリオ暦で年始から133日目（閏年では134日目）にあたり、年末まではあと232日ある。　　　 【Event】1994年 - セ・リーグ緊急理事会で、打者の頭部への危険球を投げた投手は即退場とすることを決定。　　　　【Birth】1950年 - ボビー・バレンタイン、元プロ野球選手・監督　　　　【Holidays and observance】カクテルの日　　カクテルという名称が生まれた日（アメリカの雑誌『バランス』1806年5月13日号にて）。     ";
  res.render('index', { title: 'What\'s day? (example)', wsd_msg: whatsDay_msg });
});

module.exports = router;
