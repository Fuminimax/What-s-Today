create table whatsToday.todayData
( day date, 
 cat_id int,
 hash char(255),
 dayTitle blob not null,
 dayDetail blob,
 primary key(day, cat_id, hash)
 );
 