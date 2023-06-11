#平均数
select * from (
select avg(age), '男' as 'gender', province,pid ,count(*) as num from   (select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' order by p.birthday) as tmp where 1 group by province
union all
select avg(age), '女' as 'gender', province,pid,count(*) as num from   (select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' order by p.birthday) as tmp where 1 group by province) tmp2 order by pid,gender;

##中位数
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 1
order by p.birthday limit
9865 ,1) as tmp1
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 1
order by p.birthday limit
10155 ,1) as tmp2
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 10
order by p.birthday limit
3745 ,1) as tmp3
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 10
order by p.birthday limit
4151 ,1) as tmp4
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 11
order by p.birthday limit
2853 ,1) as tmp5
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 11
order by p.birthday limit
3523 ,1) as tmp6
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 12
order by p.birthday limit
849 ,1) as tmp7
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 12
order by p.birthday limit
857 ,1) as tmp8
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 13
order by p.birthday limit
402 ,1) as tmp9
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 13
order by p.birthday limit
296 ,1) as tmp10
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 15
order by p.birthday limit
5376 ,1) as tmp11
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 15
order by p.birthday limit
5183 ,1) as tmp12
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 16
order by p.birthday limit
40 ,1) as tmp13
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 16
order by p.birthday limit
18 ,1) as tmp14
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 17
order by p.birthday limit
1805 ,1) as tmp15
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 17
order by p.birthday limit
2432 ,1) as tmp16
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 18
order by p.birthday limit
1856 ,1) as tmp17
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 18
order by p.birthday limit
1947 ,1) as tmp18
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 19
order by p.birthday limit
5800 ,1) as tmp19
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 19
order by p.birthday limit
5948 ,1) as tmp20
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 2
order by p.birthday limit
1566 ,1) as tmp21
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 2
order by p.birthday limit
1862 ,1) as tmp22
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 22
order by p.birthday limit
2682 ,1) as tmp23
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 22
order by p.birthday limit
2577 ,1) as tmp24
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 23
order by p.birthday limit
9005 ,1) as tmp25
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province, o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 23
order by p.birthday limit
6399 ,1) as tmp26
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 24
order by p.birthday limit
1594 ,1) as tmp27
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 24
order by p.birthday limit
1698 ,1) as tmp28
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 27
order by p.birthday limit
680 ,1) as tmp29
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 27
order by p.birthday limit
718 ,1) as tmp30
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 30
order by p.birthday limit
1036 ,1) as tmp31
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 30
order by p.birthday limit
1222 ,1) as tmp32
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 6
order by p.birthday limit
649 ,1) as tmp33
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 6
order by p.birthday limit
547 ,1) as tmp34
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 7
order by p.birthday limit
140 ,1) as tmp35
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 7
order by p.birthday limit
222 ,1) as tmp36
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 2 and p.created < '2019-07-01' and o.province = 9
order by p.birthday limit
4001 ,1) as tmp37
union all
select * from (  select 2019 - left(p.birthday, 4) as age, pr.province,o.province as pid from patient p inner join organizer o on p.org_id = o.id inner join province pr on pr.pid = o.province where o.city > 0 and p.birthday > 10 and p.gender = 1 and p.created < '2019-07-01' and o.province = 9
order by p.birthday limit
3271 ,1) as tmp38



##phone
SELECT AES_DECRYPT(FROM_BASE64(phone), '') FROM patient WHERE uuid = ;



## 统计体检号在分院分布
SELECT o.name as `机构名`,count(*) as `人数` FROM patient as p join organizer as o on p.org_id = o.id where p.uuid in (
) group by `机构名` order by `人数` desc;

### 某个账号的明显病变明细
SELECT
 i.check_id AS '检查号',
 i.created as '检查时间',
 p.uuid as '体检号',
 p.`name` AS '姓名',
concat(left(AES_DECRYPT(FROM_BASE64(id_number_crypt), 'a03cd73a9e14f07512ccf6f3979a48b4'), 8), '****', right(AES_DECRYPT(FROM_BASE64(id_number_crypt), 'a03cd73a9e14f07512ccf6f3979a48b4'), 6))  as '身份证号',
    if (p.`gender` = 1, '男', '女') as '性别',
  2020 - left(p.`birthday`, 4) AS '年龄',
  GROUP_CONCAT(dm.did) as dids,
  (
 SELECT
  GROUP_CONCAT( d.`name` )
 FROM
  check_disease_map AS m
  LEFT JOIN disease AS d ON d.did = m.did
 WHERE
  m.check_id = i.check_id
  AND m.`level` = 1
  AND m.position = 2
  AND m.`status` = 1

 ) AS '右眼结果',
 (
 SELECT
  GROUP_CONCAT( d.`name` )
 FROM
  check_disease_map AS m
  LEFT JOIN disease AS d ON d.did = m.did
 WHERE
  m.check_id = i.check_id
  AND m.`level` = 1
  AND m.position = 1
  AND m.`status` = 1
 ) AS '左眼结果'
FROM
 check_info AS i
 inner join check_disease_map dm on i.check_id = dm.check_id
 inner join check_info_extra e on i.check_id = e.check_id
 INNER JOIN patient AS p ON p.patient_id = i.patient_id
 INNER JOIN organizer o ON o.id = i.org_id
WHERE
 i.submit_user_id in (5556) and i.deleted = 0
and dm.did  in (8,23,62,63,64,65,67,68,69,70,71,73,74,75,76,77,78,84,85,86,87, 97,110,118,119,161,162,163,164,193,269,340,342,343,344,345,346,347,349,352,356,357,358,359,360,365,442,500,813,1698,1856)
 AND i.review_status >= 2 and p.status = 1 and p.id_number_crypt != '' and i.report_status = 1
 AND i.created > '2020-02-26 00:00:00' group by dm.check_id




## 分机型统计算法ICVD、糖尿病、高血压
SELECT c.check_id as '检查单号', 2020 - left(p.`birthday`, 4) AS '年龄', if (p.`gender` = 1, '男', '女') as '性别',
e.retina_ex->'$.db' as '糖尿病', e.retina_ex->'$.ht' as '高血压', e.retina_ex->'$.dbp' as '舒张压', e.retina_ex->'$.sbp' as '收缩压', e.retina_ex->'$.icvd' as 'ICVD',
concat(i.w, 'x', i.h) as '分辨率',
( case concat(i.w, 'x', i.h)
  when '2624x2160' then '小相机'
  when '1312x1080' then '小相机'
  when '2976x2976' then '尼德克'
  when '2304x1728' then '新视野'
  when '3280x2480' then 'kova'
  when '2100x2136' then 'canon'
  when '3648x2432' then 'canon'
  when '2304x1728' then 'canon'
  when '2592x1728' then 'canon'
  when '2592x1568' then 'canon'
  when '2976x1984' then 'canon'
  when '2976x1920' then 'canon'
  when '2736x1824' then 'canon'
  when '2736x1760' then 'canon'
  when '2576x1934' then 'topcon'
  when '2576x1854' then 'topcon'
  when '1956x1934' then 'topcon'
  when '1956x1834' then 'topcon'
  when '2048x1536' then 'topcon'
  when '2048x1484' then 'topcon'
  when '4096x3072' then '明达'
  when '2592x1944' then if(i.url like '%-COLOR-%', '新视野', '明达')
  when '2730x2048' then '新视野'
  when '2730x1948' then '新视野'
  when '2656x1992' then '新视野'
  when '2645x1984' then '新视野'
  else '其它图片'
  end
) as '机型'
, c.created as '时间'
FROM check_info as c join check_info_extra as e on c.check_id = e.check_id join check_image_map as m on e.check_id = m.check_id join image as i on m.img_id = i.img_id join patient as p on c.patient_id = p.patient_id
where c.created >= '2019-12-01 00:00:00' group by c.check_id;
-- camera 15
select p.uuid, c.check_id, c.created from patient p inner join check_info c on c.patient_id = p.patient_id inner join camera_profile ca on ca.pcode = p.uuid where ca.sn = 'FD05YWCNW01912000015' and ca.created > '2020-03-25' order by ca.id desc ;

-- 2020-03-24 --
select group_concat(i.url), p.uuid, p.birthday, p.gender, e.medical_history, e.other_history from check_info c inner join check_info_extra e on  c.check_id = e.check_id inner join patient p on p.patient_id = c.patient_id inner join check_image_map im on im.check_id = c.check_id inner join image i on im.img_id = i.img_id where c.check_id in (965886,965890,966078,595344,965953,966021,966080,966094,560260,966071,965937,965425,961015,965317,966049,966092,946798,965651,966006,966010,452075,886893,743548,894882,894839,913006,905011,904545,707602,764173,884492,801635,913763,816451,888621,694736,856058,809622,881239,864764) group by c.check_id;
-- 2020-04-01 --
select count(*) as num, label,gt from check_reid_11 where gt = 1 group by label
union all
select count(*) as num, label,gt from check_reid_11 where gt = 0 group by label;
select count(*) from check_info_extra where updated > '2020-04-11 19:20:00' and algo_status = 0;



## 查找院外同一个人
select * from  (
(select a.check_id as `检查号1`,b.check_id as `检查号2`,a.name from
(select c.check_id as check_id,p.uuid as uuid,p.name as name, p.id_number_crypt as id_number_crypt from check_info as c join patient as p on c.patient_id = p.patient_id where c.review_status not in (0, 50) and c.deleted != 1 and c.type != 1 and c.org_id not in (1, 5129, 5001, 5153, 40064) AND p.name != '' and p.name is not null and p.id_type = 1 and p.id_number_crypt is not null and p.id_number_crypt != '' ) as a
join
(select c.check_id as check_id,p.uuid as uuid,p.name as name, p.id_number_crypt as id_number_crypt from check_info as c join patient as p on c.patient_id = p.patient_id where c.review_status not in (0, 50) and c.deleted != 1 and c.type != 1 and c.org_id not in (1, 5129, 5001, 5153, 40064) AND p.name != '' and p.name is not null and p.id_type = 1 and p.id_number_crypt is not null and p.id_number_crypt != ''
 -- and c.created > '2020-03-10 00:00:00'
) as b
on a.name = b.name and a.id_number_crypt = b.id_number_crypt where a.check_id <> b.check_id and a.uuid <> b.uuid)
union
(select a.check_id as `检查号1`,b.check_id as `检查号2`,a.name from
(select c.check_id as check_id,p.uuid as uuid,p.name as name, p.phone as phone from check_info as c join patient as p on c.patient_id = p.patient_id where c.review_status not in (0, 50) and c.deleted != 1 and c.type != 1 and c.org_id not in (1, 5129, 5001, 5153, 40064) AND p.name != '' and p.name is not null and p.phone is not null and p.phone != '' ) as a
join
(select c.check_id as check_id,p.uuid as uuid,p.name as name, p.phone as phone from check_info as c join patient as p on c.patient_id = p.patient_id where c.review_status not in (0, 50) and c.deleted != 1 and c.type != 1 and c.org_id not in (1, 5129, 5001, 5153, 40064) AND p.name != '' and p.name is not null and p.phone is not null and p.phone != ''
 -- and c.created > '2020-03-10 00:00:00'
) as b
on a.name = b.name and a.phone = b.phone where a.check_id <> b.check_id and a.uuid <> b.uuid)
union
(select a.check_id as `检查号1`,b.check_id as `检查号2`,a.name from
(select c.check_id as check_id,p.uuid as uuid,p.name as name, pc.openid as openid from check_info as c join patient as p on c.patient_id = p.patient_id join patient_code as pc on p.uuid = pc.pcode where c.review_status not in (0, 50) and c.deleted != 1 and c.type != 1 and c.org_id not in (1, 5129, 5001, 5153, 40064) AND p.name != '' and p.name is not null and pc.new_wechat in (0, 1, 2) and pc.openid is not null and pc.openid != '' ) as a
join
(select c.check_id as check_id,p.uuid as uuid,p.name as name, pc.openid as openid from check_info as c join patient as p on c.patient_id = p.patient_id join patient_code as pc on p.uuid = pc.pcode where c.review_status not in (0, 50) and c.deleted != 1 and c.type != 1 and c.org_id not in (1, 5129, 5001, 5153, 40064) AND p.name != '' and p.name is not null and pc.new_wechat in (0, 1, 2) and pc.openid is not null and pc.openid != ''
 -- and c.created > '2020-03-10 00:00:00'
) as b
on a.name = b.name and a.openid = b.openid where a.check_id <> b.check_id and a.uuid <> b.uuid)
) as m group by name;

## 查找动静脉平均管径
select os.check_id,os_vein,od_vein,os_artery,od_artery from
(SELECT c.check_id as check_id,i.type as o,AVG(a.segmentation->'$.vein_diameter_mean') as os_vein, AVG(a.segmentation->'$.artery_diameter_mean') as os_artery FROM image_algorithm as a join check_info as c on c.check_id = a.check_id join image as i on i.img_id = a.img_id where c.created >= '2019-12-01 00:00:00' and c.created < '2020-01-01 00:00:00' and i.type = 1 and a.segmentation->'$.vein_diameter_mean' is not null group by c.check_id) as os
join
(SELECT c.check_id as check_id,i.type as o,AVG(a.segmentation->'$.vein_diameter_mean') as od_vein, AVG(a.segmentation->'$.artery_diameter_mean') as od_artery FROM image_algorithm as a join check_info as c on c.check_id = a.check_id join image as i on i.img_id = a.img_id where c.created >= '2019-12-01 00:00:00' and c.created < '2020-01-01 00:00:00' and i.type = 2 and a.segmentation->'$.vein_diameter_mean' is not null group by c.check_id) as od
on os.check_id = od.check_id
order by os.check_id desc;

-- ALGO E2E 测试
select count(*), im.check_id from check_image_map im inner join check_info c on im.check_id = c.check_id  where c.org_id = 40071 group by im.check_id;
select check_id, event, created, remark from check_log  where event in ('algo_result_od', 'algo_result_os', 'vcdr_od', 'vcdr_os') and id > 360383 and left(created, 10) = '2020-06-11';
select check_id , vcdr_od, vcdr_os from check_info_extra where abs(vcdr_od - vcdr_os) <= 20 and abs(vcdr_od - vcdr_os) > 10 and vcdr_od > 510 and vcdr_od < 530;

-- 测试环境 qc and gender
select c.check_id,  AES_DECRYPT(FROM_BASE64(p.name_import), '71b6b83d718f5d7eac0236f040d71283') as '姓名', p.gender, i.url, ia.blood->'$.gender' as 'gender', ia.blood->'$.age' as 'age', ia.blood->'$.quality_control_prob_1' as 'quality_control_prob_1' from  check_info c inner join check_image_map im on im.check_id = c.check_id inner join image i on im.img_id = i.img_id inner join image_algorithm ia on im.img_id = ia.img_id inner join patient p on c.patient_id = p.patient_id where c.created > '2020-07-18' and c.ext_json->'$.camera' = 'fd16' and c.check_id < 218229 and  AES_DECRYPT(FROM_BASE64(p.name_import), '71b6b83d718f5d7eac0236f040d71283') is not null;

-- UV - 爱康
select count(*) as num , o.name as '机构名称', o.aliase, cu.name as '客户', left(c.created, 7)  as '月份' from check_info c inner join organizer o on c.org_id = o.id left join customer cu on o.customer_id = cu.id where c.created > '2018-01-01' and c.created < '2020-10-01' and c.deleted = 0 and c.review_status >= 2 and c.review_status != 50 and c.org_id not in(1, 5129) group by c.org_id, left(c.created, 7) order by c.org_id, left(c.created, 7);

-- UV - 主产品
select count(*) as '数量' , o.name as '机构名称', left(c.created, 7) as '月份'  from check_info c inner join organizer o on c.org_id = o.id  where c.created > '2018-01-01' and c.created < '2020-10-01' and c.deleted = 0 and c.org_id not in (1) and c.review_status >= 2 group by c.org_id, left(c.created, 7) order by c.org_id, left(c.created, 7);
-- UV ikang -all
select left(c.created, 4) as 'year', c.package_type,  count(*) as num   from check_info c inner join organizer o on c.org_id = o.id where c.created > '2018-01-01' and c.created < '2020-11-01' and c.deleted = 0 and c.review_status != 50 and c.review_status >= 2 group by c.package_type, left(c.created, 4) order by year,c.package_type;

-- 2020-12-09
 update user set name = '平安寿险01' where phone = '13919981854';
 update user set name = '平安寿险02' where phone = '13919981855';
 update user set name = '平安寿险03' where phone = '13919981856';
 update user set name = '平安寿险04' where phone = '13919981857';
 update user set name = '平安寿险05' where phone = '13919981858';
 update user set name = '平安寿险06' where phone = '13919981859';
 update user set name = '平安寿险07' where phone = '13919981860';
 update user set name = '平安寿险08' where phone = '13919981861';
 update user set name = '平安寿险09' where phone = '13919981862';
 update user set name = '平安寿险10' where phone = '13919981863';
 update user set name = '平安寿险11' where phone = '13919981864';
 update user set name = '平安寿险12' where phone = '13919981865';
 update user set name = '平安寿险13' where phone = '13919981866';
 update user set name = '平安寿险14' where phone = '13919981867';
 update user set name = '平安寿险15' where phone = '13919981868';
 update user set name = '平安寿险16' where phone = '13919981869';
 update user set name = '平安寿险17' where phone = '13919981870';
 update user set name = '平安寿险18' where phone = '13919981871';
 update user set name = '平安寿险19' where phone = '13919981872';
 update user set name = '平安寿险20' where phone = '13919981873';
 update user set name = '平安寿险21' where phone = '13919981874';
 update user set name = '平安寿险22' where phone = '13919981875';
 update user set name = '平安寿险23' where phone = '13919981876';
 update user set name = '平安寿险24' where phone = '13919981877';
 update user set name = '平安寿险25' where phone = '13919981878';
 update user set name = '平安寿险26' where phone = '13919981879';
 update user set name = '平安寿险27' where phone = '13919981880';
 update user set name = '平安寿险28' where phone = '13919981881';
 update user set name = '平安寿险29' where phone = '13919981882';
 update user set name = '平安寿险30' where phone = '13919981883';
 update user set name = '平安寿险31' where phone = '13919981884';
 update user set name = '平安寿险32' where phone = '13919981885';
 update user set name = '平安寿险33' where phone = '13919981886';
 update user set name = '平安寿险34' where phone = '13919981887';
 update user set name = '平安寿险35' where phone = '13919981888';
 update user set name = '平安寿险36' where phone = '13919981889';
 update user set name = '平安寿险37' where phone = '13919981890';
 update user set name = '平安寿险38' where phone = '13919981891';
 update user set name = '平安寿险39' where phone = '13919981892';
 update user set name = '平安寿险40' where phone = '13919981893';
 update user set name = '平安寿险41' where phone = '13919981894';
 update user set name = '平安寿险42' where phone = '13919981895';
 update user set name = '平安寿险43' where phone = '13919981896';
 update user set name = '平安寿险44' where phone = '13919981897';
 update user set name = '平安寿险45' where phone = '13919981898';
 update user set name = '平安寿险46' where phone = '13919981899';
 update user set name = '平安寿险47' where phone = '13919981900';
 update user set name = '平安寿险48' where phone = '13919981901';
 update user set name = '平安寿险49' where phone = '13919981902';
 update user set name = '平安寿险50' where phone = '13919981903';
 update user set name = '平安寿险51' where phone = '13919981904';
 update user set name = '平安寿险52' where phone = '13919981905';
 update user set name = '平安寿险53' where phone = '13919981906';
 update user set name = '平安寿险54' where phone = '13919981907';
 update user set name = '平安寿险55' where phone = '13919981908';
 update user set name = '平安寿险56' where phone = '13919981909';
 update user set name = '平安寿险57' where phone = '13919981910';
 update user set name = '平安寿险58' where phone = '13919981911';
 update user set name = '平安寿险59' where phone = '13919981912';
 update user set name = '平安寿险60' where phone = '13919981913';
 update user set name = '平安寿险61' where phone = '13919981914';
 update user set name = '平安寿险62' where phone = '13919981915';
 update user set name = '平安寿险63' where phone = '13919981916';
 update user set name = '平安寿险64' where phone = '13919981917';
 update user set name = '平安寿险65' where phone = '13919981918';
 update user set name = '平安寿险66' where phone = '13919981919';
 update user set name = '平安寿险67' where phone = '13919981920';
 update user set name = '平安寿险68' where phone = '13919981921';
 update user set name = '平安寿险69' where phone = '13919981922';
 update user set name = '平安寿险70' where phone = '13919981923';
 update user set name = '平安寿险71' where phone = '13919981924';
 update user set name = '平安寿险72' where phone = '13919981925';
 update user set name = '平安寿险73' where phone = '13919981926';
 update user set name = '平安寿险74' where phone = '13919981927';
 update user set name = '平安寿险75' where phone = '13919981928';
 update user set name = '平安寿险76' where phone = '13919981929';
 update user set name = '平安寿险77' where phone = '13919981930';
 update user set name = '平安寿险78' where phone = '13919981931';
 update user set name = '平安寿险79' where phone = '13919981932';
 update user set name = '平安寿险80' where phone = '13919981933';
 update user set name = '平安寿险81' where phone = '13919981934';
 update user set name = '平安寿险82' where phone = '13919981935';
 update user set name = '平安寿险83' where phone = '13919981936';
 update user set name = '平安寿险84' where phone = '13919981937';
 update user set name = '平安寿险85' where phone = '13919981938';
 update user set name = '平安寿险86' where phone = '13919981939';
 update user set name = '平安寿险87' where phone = '13919981940';
 update user set name = '平安寿险88' where phone = '13919981941';
 update user set name = '平安寿险89' where phone = '13919981942';
 update user set name = '平安寿险90' where phone = '13919981943';
 update user set name = '平安寿险91' where phone = '13919981944';
 update user set name = '平安寿险92' where phone = '13919981945';
 update user set name = '平安寿险93' where phone = '13919981946';
 update user set name = '平安寿险94' where phone = '13919981947';
 update user set name = '平安寿险95' where phone = '13919981948';
 update user set name = '平安寿险北京东北营业区01' where phone = '13919981975';
 update user set name = '平安寿险北京东北营业区02' where phone = '13919981976';
 update user set name = '平安寿险北京京东营业区01' where phone = '13919981977';
 update user set name = '平安寿险北京京东营业区02' where phone = '13919981978';
 update user set name = '平安寿险北京京东营业区03' where phone = '13919981979';
 update user set name = '平安寿险北京京北营业区01' where phone = '13919981980';
 update user set name = '平安寿险北京京北营业区02' where phone = '13919981981';
 update user set name = '平安寿险北京京北营业区03' where phone = '13919981982';
 update user set name = '平安寿险北京苏州营业区01' where phone = '13919981983';
 update user set name = '平安寿险北京苏州营业区02' where phone = '13919981984';
 update user set name = '平安寿险96' where phone = '13919981985';
 update user set name = '平安寿险97' where phone = '13919981986';
 update user set name = '平安寿险98' where phone = '13919981987';
 update user set name = '平安寿险99' where phone = '13919981990';
 update user set name = '平安寿险100' where phone = '13919981991';
 update user set name = '平安寿险101' where phone = '13919981992';
 update user set name = '平安寿险102' where phone = '13919981994';
 update user set name = '平安寿险103' where phone = '13919981995';
 update user set name = '平安寿险104' where phone = '13919981996';
 update user set name = '平安寿险105' where phone = '13919981997';
 update user set name = '平安寿险北京北太营业区01' where phone = '13919982001';
 update user set name = '平安寿险北京北太营业区02' where phone = '13919982002';
 update user set name = '平安寿险北京北太营业区03' where phone = '13919982003';
 update user set name = '平安寿险北京国峰营业区01' where phone = '13919982004';
 update user set name = '平安寿险北京甘家口营业区01' where phone = '13919982005';
 update user set name = '平安寿险北京甘家口营业区02' where phone = '13919982006';
 update user set name = '平安寿险北京亮马桥营业区01' where phone = '13919982008';
 update user set name = '平安寿险北京东便门营业区01' where phone = '13919982009';
 update user set name = '平安寿险北京健德桥营业区01' where phone = '13919982010';
 update user set name = '平安寿险北京健德桥营业区02' where phone = '13919982011';
 update user set name = '平安寿险北京健德桥营业区03' where phone = '13919982012';
 update user set name = '平安寿险北京健德桥营业区04' where phone = '13919982013';
 update user set name = '平安寿险北京东便门营业区02' where phone = '13919982014';
 update user set name = '平安寿险北京亮马桥营业区02' where phone = '13919982015';
 update user set name = '平安寿险北京亮马桥营业区03' where phone = '13919982016';
 update user set name = '平安寿险北京健德桥营业区05' where phone = '13919982017';
-- 2020-12-09 慧心瞳+FD16
select o.name as '机构', u.name as '账号', if (cu.name is not null, cu.name, '-') as '客户' , w.name as '销售负责人', max(c.created) as '最新使用时间', count(*) as num from check_info c inner join organizer o on c.org_id = o.id left join worker w on o.sales_id = w.id left join customer cu on cu.id = o.customer_id inner join user u on c.submit_user_id = u.user_id where c.deleted= 0 and c.review_status= 2 and c.created > '2020-06-01' and c.ext_json->'$.camera' = 'fd16' and (c.ext_json->'$.report_type' = 'huixintong' or c.ext_json->'$.report_type' is null) and o.customer_id not in (15, 17) and o.id not in (1, 5129) group by c.submit_user_id order by num desc;
-- 2020-12-11
select count(*) as 'UV数', o.id as '机构代码', o.name as '机构名称', o.aliase, cu.name as '客户',  left(c.created, 4)  as '年份', left(c.created, 7)  as '月份' , ss.name from check_info c inner join organizer o on c.org_id = o.id left join customer cu on o.customer_id = cu.id left join sales_status ss on convert(o.config->'$.sales_status', signed) = ss.id where c.created > '2018-01-01' and c.created < '2020-12-01' and c.deleted = 0 and c.review_status >= 2 and c.review_status != 50 and c.org_id not in(1, 5129) group by c.org_id, left(c.created, 7) order by c.org_id, left(c.created, 7);
-- 2020-12-18 开通签名情况的机构列表
select o.id as '机构ID', o.name as '机构', if (cu.name is not null, cu.name, '-') as '客户' , if (ss.name is not null, ss.name, '-') as '收费状态', if (w.name is not null, w.name, '-') as '销售负责人', o.display_doctor_review as '展示医生签名', if (convert(config->'$.remote_review', signed) > 1, '开通', '未开通') as '开启远程阅片' from organizer o left join worker w on o.sales_id = w.id left join customer cu on cu.id = o.customer_id left join sales_status ss on o.config->>'$.sales_status' = ss.id where o.id not in (1, 5129, 5000)  and o.status = 1;

-- 2020-12-23 钟翔鹏迁移机构
update check_info set org_id = 5001 where check_id IN (367132,367148,372620,372624,372627,372636,372638,372642,372645,372650,372657,372660,372667,372668,372671,372677,372681,372694,372697,372699,372703,372705,372724,372728,372734,372738,372741,372744,372752,372755,372761,372767,372770,372772,372775,372776,372777,372780,372783,372787,372792,372794,372801,372804,372805,372807,372822,372824,372825,372826,372842,372846,372849,372853,372856,372857,372861,372865,372866,372867,372869,372872,372874,372875,372878,372883,372885,372888,372889,372893,372895,372896,372899,372901,372904,372910,372914,372918,372923,372926,372929,372930,372933,372934,372937,372939,372941,372942,372943,372945,372946,372949,372956,372959,372962,372965,372967,372969,372972,372979,372980,372981,426003,426025,429551,429588,429693,430133,430303,430335,430369,430399,430411,430417,430432,430474,430497,430592,430603,430617,430674,430733,430752,430776,430781,430796,430799,430802,430806,430823,430824,430826,430827,430828,430829,430830,430831,430832,430834,430835,430836,430838,430839,430840,430841,430842,430843,430844,430850,430852,430856,430865,430867,430868,430869,430871,430872,430873,448653,448794,448806,448813,448837,448853,448869,448896,448914,448928,448945,448958,448974,448985,449002,449009,449018,449031,449041,449051,449055,449062,449068,449072,449079,449115,449119,449124,449126,449129,449130,449131,449132,449133,449137,449138,449139,449141,449143,449144,449147,449152,449153,449154,449157,449158,449159,449160,449161,449162,449163,449165,449166,449167,449168,449169,449170,449171,449172,449173,449175,449176,449177,449178,449179,449180,449181,449182,449183,449184,449185,449187,449188,449190,449191,449192,449193,449194,449195,449196,449197,449199,449201,449202,687142,712976,714519,715898,715931,715947,715967,715994,716016,716031,716041,716051,716069,716077,716111,716139,716148,716157,716166,716182,716191,716203,716214,716218,716229,716242,716261,716298,716328,716418,716443,716444,716445,716446,716451,716452,716453,718157,718617,719478,719500,720582,720602,720603,720604,720605,720606,720607,720608,770075,770080,805073,805074,805075,820592,821873,822009,822182,822212,822452,822532,822553,822581,822602,822628,822645,822694,822922,822968,823062,823167,823418,823641,823709,823742,823763,823778,823832,823879,823953,823988,824054,824087,824180,824252,824272,824315,824418,824477,824489,824548,824561,824574,824593,824641,824656,824671,824681,824687,824699,824718,824744,824757,824766,824772,824810,824813,824824,824831,824844,824852,824891,824900,824961,824973,824985,825011,825041,825042,825044,825046,825047,825049,825052,825053,825054,825057,825062,825067,825070,825072,825074,825077,825087,825091,825100,825104,825106,825108,825109,825111,825113,825114,829869,829908,829980,830014,830044,830063,830092,830109,830144,830221,830248,830273,830317,830338,830407,830416,830440,830460,830493,830514,830568,830623,830648,830665,830713,830734,830752,830805,830885,830942,830973,831001,831003,831017,831027,831035,831049,831071,831085,831090,831103,831110,831184,831251,831256,831285,831337,831356,831357,831379,831388,831397,831401,831433,831467,831473,831514,831628,831633,831639,831641,831646,831652,831730,831731,831732,831737,831738,831740,831744,831746,831748,831749,831751,831754,831756,831757,831758,831759,831760,831762,831763,831766,831769,831771,831772,831773,831775,851170,851182,851193,851200,851208,851213,851218,851222,851227,851233,851238,851241,851248,851252,851256,851259,851263,851265,851267,851269,851270,851271,851273,851276,851277,851279,851280,851285,851287,851290,851294,851296,851297,851299,851303,851305,851307,851310,851312,851316,851317,851319,851320,851321,851322,851323,851324,851325,851326,851327,851328,851329,851330,851331,851332,851333,851334,851335,851336,851337,851338,851339,851340,851341,851342,851343,851344,851345,851346,851347,851348,851349,851350,851351,851352,851353,851354,851355,851356,851357,851358,851359,851360,851361,851362,851363,851364,851365,851366,851367,851368,851369,851370,851371,851372,851373,851374,851375,851376,851377,851378,851379,851380,851381,851382,851383,851384,875325,881330,881376,890386,890488,890499,890793,890812,890829,890839,890841,890849,890887,890948,890951,890956,890958,890968,890971,890972,890974,890976,890977,890980,890981,890982,890986,890987,890988,890989,890990,890991,890992,890994,890995,890996,890997,890999,891000,891001,891002,891003,891004,891005,891006,891007,891008,891009,891010,891011,891012,891013,891014,891017,891018,891019,891020,891021,891022,891023,891024,891025,891027,891028,891029,891030,891031,891032,891036,891039,891041,891042,898848,900171,900183,900259,901139,901239,901746,901764,901814,901905,901926,902011,902400,902415,902429,902442,902462,902468,902474,902486,902497,902510,902519,902532,902539,902565,902580,902627,902667,902747,902750,902751,902763,902765,902767,902771,902773,902778,902779,902780,902831,902841,902842,902845,902847,902848,902875,902883,911183,923538,923539,923540,923541,923542,923543,923544,923545,923546,923547,923548,923549,923550,923551,923552,923553,923554,930985,930990,931000,931006,931010,931013,931016,931020,931025,931027,931029,931031,931033,931038,931039,931043,931044,931046,931050,931051,931052,931053,931057,931059,931061,931062,931064,931065,931066,931067,931068,931069,931070,931071,931072,931073,931074,931075,931076,931077,931078,931079,931081,931082,931083,931084,931085,931086,931087,938991,939006,939101,939119,939174,939201,939263,939281,939324,939417,939445,939491,939531,939544,939623,939718,939745,939795,939817,939831,939873,939905,939924,939944,940005,940066,940075,940088,940118,940178,940205,940216,940237,940241,940277,940316,940524,940525,940526,940527,940528,940529,940530,940531,940532,940533,940534,940535,940536,940537,940538,940539,940540,940541,940542,940543,940544,940545,940546,940547,940548,940549,940550,940551,940552,940553,940554,940555,940557,947935,955619,955621,955622,955623,955624,955728,955805,955816,955884,955923,955942,955945,955959,955966,955974,955977,955978,955979,955980,955984,955985,955986,955988,955990,955991,955992,955993,955994,955995,955996,955997,955998,955999,956000,956001,956002,956007,956008,956012,956014,956015,956016,956017,956018,956019,956020,956021,956022,956023,956024,956026,956027,956028,956029,956030,956032,956033,956034,956036,956037,956038,956042,956043,956047,956049,956055,956060,956062,956065,956070,956075,956081,956083,956094,956098,956104,956108,956265,956266,956267,956268,956269,956270,956271,956272,956273,956274,956275,956278,961065,961066,961067,961068,961069,961070,961071,961072,961073,961074,961075,961076,961077,961078,970618,970789,970892,971297,971819,971849,972387,972422,972456,972458,972459,972461,972462,972464,972467,972468,972469,972471,972474,972478,972479,972485,972488,972489,972492,972493,972494,972495,972496,972500,972503,972505,972508,972509,972526,972535,972538,972539,1021709,1021710,1021711,1021712,1021713,1021714,1021715,1021716,1021717,1021718,1021719,1021720,1021721,1021722,1021724,1021725,1021726,1021728,1021729,1021730,1021731,1021732,1021733,1021734,1021735,1021736,1021737,1075512,1075734,1075736,1105270,1105271,1105274,1105277,1105278,1105280,1105281,1105283,1105284,1105285,1105286,1105287,1105289,1105290,1105291,1105292,1105293,1105294,1105297,1105299,1105300,1105304,1105475,1105476,1105477,1105478,1105479,1105480,1105481,1105482,1105483,1105484,1105485,1105486,1105487,1105488,1105489,1105490,1105491,1105492,1105493,1105494,1105495,1105496,1105497,1105498,1105499,1105500,1109789,1111756,1112016,1112189,1112344,1112368,1112371,1112876,1114117,1114187,1114405,1114410,1114418,1114436,1114447,1114451,1114457,1114461,1114466,1114477,1114479,1114484,1114487,1126617,1127032,1128830,1134429,1134444,1134523,1134540,1134604,1134681,1134688,1134690,1134774,1134779,1134780,1134781,1134806,1134851,1134874,1134948,1134953,1134956,1134965,1134994,1135007,1135008,1135014,1135030,1135032,1135036,1135037,1142424,1142464,1144915,1156533,1157132,1157189,1157196,1157202,1159815,1160248,1160255,1160264,1160272,1160278,1160283,1160288,1160320,1162960,1163003,1163668,1163674,1163690,1163698,1163705,1163709,1163712,1163716,1163720,1163722,1163726,1163728,1163735,1163738,1163739,1163744,1163750,1163755,1163758,1163761,1163762,1163763,1163765,1163766,1163768,1163770,1163773,1163777,1163780,1163783,1163785,1163789,1163791,1163795,1163797,1163801,1163805,1163808,1163811,1163815,1163818,1163819,1163821,1163823,1163824,1163826,1163828,1163829,1163830,1163831,1163832,1163833,1163835,1163839,1163842,1163845,1163846,1163847,1163848,1163852,1163854,1163856,1163857,1163858,1163861,1163864,1163867,1168262,1168466,1168567,1168609,1168787,1168957,1168988,1169049,1169073,1169168,1169224,1169242,1169293,1169325,1169335,1169359,1169377,1169405,1169422,1169471,1169489,1169514,1169518,1169532,1169567,1169601,1169623,1169644,1169692,1169718,1169748,1169770,1169799,1169827,1169852,1169877,1169891,1169916,1169934,1169961,1169982,1169996,1170032,1170060,1170072,1170088,1170107,1170133,1170143,1170161,1170168,1170177,1170189,1170204,1170222,1170236,1170297,1170317,1170327,1170350,1170361,1170373,1170389,1170404,1170419,1170446,1170463,1170475,1170481,1170490,1170515,1170547,1170559,1170564,1170567,1170572,1170579,1170580,1170585,1170590,1170595,1170599,1170601,1170604,1170606,1170611,1170614,1170620,1170623,1170629,1170632,1174107,1174139,1174210,1174235,1174302,1174321,1174341,1174370,1174396,1174414,1174487,1174523,1174561,1174592,1174596,1174630,1174633,1174640,1174670,1174674,1174676,1174680,1183423,1183775,1183794,1183931,1193920,1194065,1194174,1195372,1205775,1206289,1206313,1206328,1206338,1206350,1206572,1206579,1206971,1206978,1207018,1207291,1207298,1207307,1207312,1207315,1207327,1207330,1207336,1207346,1207347,1207352,1207354,1207364,1207370,1207377,1207382,1207383,1207384,1207392,1207395,1227587,1227591,1227595,1227599,1227607,1227611,1227617,1227621,1227626,1227632,1227635,1227641,1227643,1227650,1227655,1227660,1227664,1227675,1227688,1227696,1227701,1227706,1227710,1227720,1227735,1227739,1227742,1227747,1227752,1227764,1227767,1227771,1227802,1227815,1227836,1227849,1227865,1227873,1227878,1227880,1227891,1227907,1227908,1227913,1227923,1227925,1227929,1227930,1227958,1227960,1230487,1231894,1232441,1232470,1232515,1232766,1232768,1232770,1232782,1232785,1232788,1232795,1232799,1232801,1232806,1232812,1232814,1232818,1232819,1232823,1232826,1232828,1232832,1232837,1232840,1232843,1232845,1232848,1232854,1232858,1232860,1232864,1232875,1233090,1233093,1233098,1233105,1233109,1234935,1234937,1235052,1235088,1235182,1235225,1235464,1235758,1235763,1235764,1235777,1235785,1235791,1235800,1235807,1235812,1235815,1235828,1235833,1235838,1235846,1235853,1235863,1235874,1235880,1235942,1235975,1235990,1236034,1236063,1236110,1236141,1236146,1236150,1236175,1236180,1236197,1236216,1236283,1236324,1242390,1242399,1243011,1243026,1243032,1243036,1243042,1243056,1243059,1243070,1243083,1243095,1243128,1243137,1243148,1243152,1243165,1243169,1243218,1243221,1243223,1243228,1245315,1253130,1253136,1253140,1253151,1253159,1253173,1253177,1253184,1253296,1253313,1264733,1264772,1264923,1264951,1264953,1264978,1265133,1265384,1265507,1265573,1265590,1265669,1265719,1265734,1265780,1265838,1265949,1266000,1266014,1266027,1266043,1266069,1266090,1266108,1266117,1266141,1266192,1266215,1266237,1266245,1266252,1266264,1266273,1266281,1266289,1266295,1266299,1266305,1266332,1266340,1266346,1266357,1266364,1266372,1266376,1266386,1266416,1266466,1266467,1266538,1266549,1266564,1266579,1266598,1266920,1266922,1266938,1266970,1266973,1266977,1266998,1267042,1267059,1267064,1267068,1267083,1267092,1267094,1267149,1267151,1267153,1274792,1274830,1275069,1275082,1275090,1275100,1275112,1275129,1275194,1275207,1275272,1275309,1275322,1275345,1275351,1275359,1275366,1275371,1275375,1275379,1275383,1275391,1275399,1275400,1275403,1275409,1275412,1275424,1275432,1275443,1275454,1275461,1275468,1275473,1275482,1275491,1275494,1275500,1275521,1275532,1278726,1279039,1282984,1283697,1283840,1293309,1293318,1293321,1293325,1293328,1293331,1293332,1293335,1293340,1293342,1293345,1293349,1293351,1293355,1293357,1293359,1293362,1293366,1293368,1293374,1293376,1293380,1293384,1293386,1293393,1293397,1293521,1293531,1296260,1296518,1296546,1296565,1296583,1296594,1296603,1296611,1296616,1296754,1296766,1296909,1296913,1296920,1297455,1297589,1297613,1301069,1301072,1301096,1301097,1301098,1301100,1301101,1301103,1301109,1301110,1301112,1306715,1306763,1306899,1307036,1307166,1307318,1307395,1307486,1307579,1307588,1307597,1307618,1307685,1307708,1307741,1307803,1307813,1307851,1308243,1308534,1308793,1336755,1337112,1337175,1337196,1337226,1337269,1337288,1337301,1337309,1337331,1337444,1337451,1337457,1337468,1337473,1337483,1337488,1337498,1337504,1337515,1343103,1343387,1343390,1348256,1348273,1348300,1348813,1348821,1353063,1360618,1361039,1361504,1361514,1361515,1361517,1363233,1363335,1363705,1363874,1363930,1364055,1364217,1364250,1364291,1364409,1364526,1364590,1364664,1364710,1364775,1365304,1365633,1365670,1366226,1366287,1366734,1366824,1366850,1366927,1366940,1366953,1366976,1367164,1367848,1367855,1372164,1372181,1372192,1372296,1372298,1372308,1372387,1372413,1372442,1372698,1372706,1372711,1372716,1372728,1372743,1372748,1372760,1372765,1372779,1372785,1372787,1372794,1372810,1372815,1372820,1372822,1372827,1372840,1372846,1372850,1372854,1372860,1372863,1372868,1372872,1372880,1372886,1372891,1372895,1372898,1372916,1372922,1372926,1372944,1372947,1372979,1372988,1387942,1392597,1392607,1392611,1392612,1392613,1392618,1397009,1397013,1397049,1397058,1397067,1397086,1397103,1397108,1397110,1397127,1397130,1397153,1397162,1397666,1397684,1401695,1402029,1402047,1402064,1402192,1402838,1408156,1417714,1426105,1431615,1431622,1431650,1431891,1432016,1432025,1436720,1436732,1436735,1436748,1436753,1436766,1436770,1436774,1436789,1436793,1436814,1436848,1436895,1436956,1436969,1436988,1437004,1437026,1437031,1437042,1437054,1437058,1437064,1437068,1437074,1437077,1437081,1437088,1437090,1437093,1437098,1437104,1437109,1437114,1437116,1437117,1437125,1437127,1437132,1437135,1437140,1437148,1437151,1437155,1437161,1437165,1437168,1437171,1437174,1437180,1437184,1437187,1437193,1437198,1437201,1437206,1437212,1437214,1437221,1437226,1437231,1437236,1437240,1437244,1437250,1437255,1437262,1437268,1437274,1437275,1437280,1437284,1437294,1437302,1437306,1437308,1437314,1437318,1437320,1437325,1437328,1437333,1437336,1437338,1437342,1437344,1437348,1437352,1437356,1437358,1437366,1437371,1437376,1437381,1437386,1437390,1437393,1437394,1437402,1437408,1437412,1437419,1437421,1437425,1437429,1437433,1437436,1437441,1437445,1437451,1437453,1437457,1437465,1437469,1437536,1437584,1440514,1440596,1442524,1442554,1442566,1442605,1442635,1442664,1442816,1442950,1442998,1443050,1443067,1443130,1443132,1443134,1443135,1443138,1443139,1443140,1443141,1443143,1443145,1443146,1443147,1443149,1443150,1443151,1443153,1443154,1443157,1443158,1443160,1443161,1443162,1443166,1443168,1443170,1443173,1443174,1443175,1445597,1445648,1445759,1447446,1447476,1449310) and package_type = 3;


-- 2020-12-25 SLA
select a.st as `日期`,a.s as `总数`,if (b.t is not null, b.t, 0) as `超时`,concat(truncate((if (b.t is not null, b.t, 0)/a.s) * 100,2),'%') as `超时比例`  from (
select left(start_time, 10) as st,count(*) as s from check_info where start_time >="2020-10-01" and start_time < "2021-01-01" group by left(start_time, 10)
) as a
left join (
select left(start_time, 10) as tt,count(*) as t from check_info where start_time >="2020-10-01" and start_time < "2021-01-01" and (unix_timestamp(review_time) - unix_timestamp(start_time)) > 120  group by left(start_time, 10)
) as b
on a.st = b.tt;

-- 2021-01-15 统计小程序开关情况
select count(*)  as num, o.config->>'$.rigister_miniprogram' as '小程序开关' from camera c inner join user u on c.user_id = u.user_id inner join organizer o on u.org_id = o.id where c.status = 0 and u.user_id not in (5555) and o.id not in (40086) group by o.config->>'$.rigister_miniprogram';


-- 统计季度超时
select a.st as `日期`,a.s as `总数`,if (b.t is not null, b.t, 0) as `超时`,concat(truncate((if (b.t is not null, b.t, 0)/a.s) * 100,2),'%') as `超时比例`  from (
select left(start_time, 10) as st,count(*) as s from check_info where start_time >="2021-01-01" and start_time < "2021-04-01" group by left(start_time, 10)
) as a
left join (
select left(start_time, 10) as tt,count(*) as t from check_info where start_time >="2021-01-01" and start_time < "2021-04-01" and (unix_timestamp(review_time) - unix_timestamp(start_time)) > 120  group by left(start_time, 10)
) as b
on a.st = b.tt;

-- 日报sql
select o.name as '机构', c.package_type as '套餐', count(*) as 'UV', w1.name as '销售', city.city as '城市' from check_info c inner join organizer o on c.org_id = o.id left join worker w1 on o.sales_id = w1.id  left join city on o.city =  city.cid where o.customer_id = 1 and c.deleted = 0 and c.start_time > '2021-04-17' and c.review_status >= 2 and c.review_status < 50 group by c.org_id, c.package_type order by c.org_id, c.package_type;

select u.user_id, u.name , u.org_id, u.province, u.city, u.scope, u.sales_id, u.operator_id, o.name as '机构名称',  o.sales_id as '机构销售ID', o.operator_id as '机构运营ID', o.province as '机构省份', o.city as '机构城市' , o.scope as '区域ID'  from user u inner join organizer o on u.org_id = o.id where u.org_id not in (40338, 40456) and o.customer_id = 5;

// 机构，创建时间，合同名，客户，状态，负责销售，负责运营，最后一次uv产生时间，使用产品（慧心瞳，鹰瞳，众佑)，计费方式
select o.id as '机构ID', o.created as '创建时间', o.name as '机构', if (o.aliase is not null, o.aliase, '-') as '合同名', if (cu.name is not null, cu.name, '-') as '客户' , if (ss.name is not null, ss.name, '-') as '收费状态', if (w.name is not null, w.name, '-') as '销售负责人', if (w2.name is not null, w2.name, '-') as '运营负责人', o.balance_type as '计费方式', max(c.created) as '最后一次UV时间' from organizer o left join worker w on o.sales_id = w.id left join worker w2 on o.operator_id = w2.id  left join customer cu on cu.id = o.customer_id left join sales_status ss on o.config->>'$.sales_status' = ss.id left join check_info c on o.id = c.org_id where o.id not in (1, 5129, 5000)  and o.status = 1 and c.created > '2019-01-01' group by o.id;

--眼知健
CREATE TABLE `pangu_nvwa_user_map` (
  `map_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pangu_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '盘古user_id',
  `nvwa_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '女娲user_id',
  `deleted` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '状态0=正常1=删除 ',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`map_id`),
  KEY `userid` (`pangu_user_id`,`nvwa_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='盘古女娲账户map关系';
CREATE TABLE `pangu_nvwa_check_map` (
  `check_map_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pangu_check_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '盘古user_id',
  `nvwa_check_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '女娲user_id',
  `h5_status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'h5状态 0=未接收1=已接收 ',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `h5_url` varchar(256) NOT NULL DEFAULT '',
  PRIMARY KEY (`check_map_id`),
  KEY `check_id` (`pangu_check_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='盘古女娲检查单map关系';
-- 众佑PDF模板更换
update organizer set new_template=9 where `customer_id` in(15,17);
-- 鹰瞳健康数据表
alter table yt_check_info_statistics add `anemia` tinyint(4) NOT NULL DEFAULT '0' COMMENT '血气不足'

--重大阳性预警图片链接
ALTER TABLE alarm ADD  `alarm_url` varchar(255) NOT NULL DEFAULT '' COMMENT '预警图片地址'
--海外推送记录
CREATE TABLE `oversea_push_third_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `check_id` int(11) NOT NULL DEFAULT '0' COMMENT '检查单id',
  `org_id` int(11) NOT NULL DEFAULT '0' COMMENT '机构id',
  `push_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '推送状态0:默认1：成功2：失败',
  `error_no` tinyint(4) NOT NULL DEFAULT '0' COMMENT '错误码（失败次数）',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated` datetime NOT NULL DEFAULT '1971-01-01 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `check_org` (`check_id`,`org_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='海外三方推送记录表';

alter table yt_check_info_statistics modify new_supersedes_old tinyint(4) NOT NULL DEFAULT '0' COMMENT '新陈代谢指数';
alter table yt_check_info_statistics modify hormonal_balance tinyint(4) NOT NULL DEFAULT '0' COMMENT '荷尔蒙平衡指数';
alter table yt_check_info_statistics modify microvascular_injury tinyint(4) NOT NULL DEFAULT '0' COMMENT '微血管损伤风险';

