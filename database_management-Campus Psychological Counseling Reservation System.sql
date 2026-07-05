-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2026-06-24 12:26:23
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `consultation`
--

-- --------------------------------------------------------

--
-- 資料表結構 `administrative`
--

CREATE TABLE `administrative` (
  `m_id` char(10) NOT NULL,
  `admin_position` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `administrative`
--

INSERT INTO `administrative` (`m_id`, `admin_position`) VALUES
('A12345', '資安師'),
('A32165', '網頁維護人員'),
('A87564', '心輔組長'),
('A95636', '主管');

-- --------------------------------------------------------

--
-- 資料表結構 `appointmentapply`
--

CREATE TABLE `appointmentapply` (
  `aa_id` char(10) NOT NULL,
  `aa_type` enum('人際關係','學業壓力','情緒困擾','自我探索') NOT NULL,
  `aa_summary` varchar(50) NOT NULL,
  `aa_date` date NOT NULL,
  `aa_period` enum('第一節課','第二節課','第三節課','第四節課','第五節課','第六節課','第七節課','第八節課') NOT NULL,
  `m_id` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `appointmentapply`
--

INSERT INTO `appointmentapply` (`aa_id`, `aa_type`, `aa_summary`, `aa_date`, `aa_period`, `m_id`) VALUES
('aa001', '人際關係', '最近跟朋友吵架了，感覺自己莫名其妙被朋友疏遠，每天都很難過但也不知道怎麼辦', '2026-05-18', '第四節課', 'H34567890'),
('aa002', '自我探索', '明明快畢業了，但我不確定自己的優勢與興趣，對未來的發展方向感到迷茫 ', '2026-05-25', '第五節課', 'H34985632'),
('aa003', '學業壓力', '作業很多考試分數很難拿，每天都睡不好，睡眠時間極少卻常失眠，想休學但家長不會同意', '2026-05-26', '第三節課', 'H34985632'),
('aa004', '人際關係', '跟前任分手結果他變成恐怖情人，雖然已無聯繫但對我造成陰影，無法信任與依賴其他人，更無法建立親密關係', '2026-05-27', '第五節課', 'H34001234'),
('aa005', '情緒困擾', '有時候脾氣會變得很暴躁，食慾也忽大忽小，一直很想睡覺，有時候也會變得很悲觀', '2026-06-09', '第三節課', 'T14231212'),
('aa017', '人際關係', '朋友之後都要搬出去住，一瞬間沒有室友也失去朋友的感覺，可能原本就沒什麼共同話題，隨著期末考來臨覺得很', '2026-06-01', '第四節課', 'B12376587'),
('aa032', '學業壓力', '課業繁重且名次下滑，背負著家人與自己對成績的期許 ', '2026-05-04', '第二節課', 'H34985632'),
('aa041', '情緒困擾', '常無故陷入低落與焦慮，難以控制莫名湧現的負面情緒 ', '2026-05-08', '第五節課', 'H34567890'),
('aa054', '自我探索', '對於目前就讀的科系毫無興趣，考慮休學或轉系', '2026-05-29', '第三節課', 'H34567890'),
('aa055', '自我探索', '即將畢業卻找不到未來職涯方向，極度焦慮', '2026-05-30', '第四節課', 'B12376587'),
('aa056', '情緒困擾', '最近莫名經常流淚、提不起勁，想尋求協助', '2026-05-30', '第七節課', 'F41322154'),
('aa057', '人際關係', '覺得自己有社交恐懼，在人群中會過度緊張', '2026-06-01', '第一節課', 'H34001234'),
('aa058', '人際關係', '不適應大學的團體生活，經常感到孤單', '2026-06-01', '第二節課', 'H34567890'),
('aa059', '學業壓力', '微積分快被當掉了，非常害怕被退學', '2026-06-01', '第五節課', 'H34985632'),
('aa060', '學業壓力', '課業繁重到無法喘息，時間管理出現大問題', '2026-06-01', '第六節課', 'B12376587'),
('aa061', '情緒困擾', '容易因小事暴怒，影響到周遭朋友，想控制情緒', '2026-06-09', '第二節課', 'F41322154'),
('aa063', '自我探索', '希望能更了解自己的性格優缺點與潛在特質', '2026-06-10', '第三節課', 'H34567890'),
('aa064', '自我探索', '面臨考研還是直接就業的兩難，想找人談談', '2026-06-17', '第二節課', 'B12376587'),
('aa065', '情緒困擾', '面臨期末考週，整個人焦慮到無法專心看書', '2026-06-17', '第三節課', 'F41322154'),
('aa101', '人際關係', '跟室友因為衛生習慣吵架，關係冰凍', '2026-05-10', '第三節課', 'H34001234'),
('aa102', '自我探索', '不知道未來要考研還是就業，極度焦慮', '2026-06-12', '第四節課', 'F41322154'),
('aa103', '情緒困擾', '考試壓力大到失眠，想找人談談', '2026-06-15', '第一節課', 'B12376587'),
('aa201', '學業壓力', '微積分可能被當掉，心情很沉重', '2025-11-10', '第二節課', 'H34567890'),
('aa202', '情緒困擾', '天氣轉變後情緒一直很低落，提不起勁', '2025-12-05', '第五節課', 'H34985632'),
('aa301', '情緒困擾', '最近常莫名感到焦慮低落，想學習如何控制脾氣', '2026-05-20', '第五節課', 'T14231212'),
('aa302', '人際關係', '系上分組找不到組員，覺得自己被排擠很孤單', '2026-06-01', '第二節課', 'T14231212'),
('aa303', '自我探索', '對於會計專業沒有熱情，對未來職涯方向很迷茫', '2026-06-10', '第三節課', 'T14231212');

-- --------------------------------------------------------

--
-- 資料表結構 `appointmentrecord`
--

CREATE TABLE `appointmentrecord` (
  `ar_id` char(5) NOT NULL,
  `ar_date` date NOT NULL,
  `aa_period` enum('第一節課','第二節課','第三節課','第四節課','第五節課','第六節課','第七節課','第八節課') NOT NULL,
  `ar_state` enum('初步洽詢','媒合中','預約成功','諮商中','結案/暫停') NOT NULL,
  `ar_result` enum('未通知','已通知','通知失敗') NOT NULL,
  `m_id` char(10) NOT NULL,
  `c_id` char(6) NOT NULL,
  `aa_id` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `appointmentrecord`
--

INSERT INTO `appointmentrecord` (`ar_id`, `ar_date`, `aa_period`, `ar_state`, `ar_result`, `m_id`, `c_id`, `aa_id`) VALUES
('', '0000-00-00', '第一節課', '預約成功', '未通知', 'B12376587', 'D45357', 'aa017'),
('ar001', '2026-05-18', '第四節課', '預約成功', '已通知', 'H34567890', 'D45357', 'aa001'),
('ar002', '2026-05-25', '第五節課', '預約成功', '已通知', 'H34985632', 'D12094', 'aa002'),
('ar032', '2026-05-04', '第二節課', '預約成功', '已通知', 'H34985632', 'D45357', 'aa032'),
('ar041', '2026-05-08', '第五節課', '結案/暫停', '已通知', 'H34567890', 'D45368', 'aa041'),
('ar054', '2026-05-29', '第三節課', '結案/暫停', '已通知', 'H34567890', 'D77432', 'aa054'),
('ar055', '2026-05-30', '第四節課', '結案/暫停', '已通知', 'B12376587', 'D77432', 'aa055'),
('ar056', '2026-05-30', '第七節課', '結案/暫停', '已通知', 'F41322154', 'D77432', 'aa056'),
('ar057', '2026-06-01', '第一節課', '結案/暫停', '已通知', 'H34001234', 'D45357', 'aa057'),
('ar058', '2026-06-01', '第二節課', '結案/暫停', '已通知', 'H34567890', 'D45357', 'aa058'),
('ar059', '2026-06-01', '第五節課', '結案/暫停', '已通知', 'H34985632', 'D45357', 'aa059'),
('ar060', '2026-06-01', '第六節課', '結案/暫停', '已通知', 'B12376587', 'D45357', 'aa060'),
('ar061', '2026-06-09', '第二節課', '結案/暫停', '已通知', 'F41322154', 'D77432', 'aa061'),
('ar063', '2026-06-10', '第三節課', '結案/暫停', '已通知', 'H34567890', 'D45368', 'aa063'),
('ar064', '2026-06-17', '第二節課', '結案/暫停', '已通知', 'B12376587', 'D45368', 'aa064'),
('ar065', '2026-06-17', '第三節課', '結案/暫停', '已通知', 'F41322154', 'D45368', 'aa065'),
('ar101', '2026-05-10', '第三節課', '結案/暫停', '已通知', 'H34001234', 'D12094', 'aa101'),
('ar102', '2026-06-12', '第四節課', '結案/暫停', '已通知', 'F41322154', 'D22841', 'aa102'),
('ar103', '2026-06-15', '第一節課', '結案/暫停', '已通知', 'B12376587', 'D77432', 'aa103'),
('ar201', '2025-11-10', '第二節課', '結案/暫停', '已通知', 'H34567890', 'D45357', 'aa201'),
('ar202', '2025-12-05', '第五節課', '結案/暫停', '已通知', 'H34985632', 'D45368', 'aa202'),
('ar301', '2026-05-20', '第五節課', '預約成功', '已通知', 'T14231212', 'D45368', 'aa301'),
('ar302', '2026-06-01', '第二節課', '預約成功', '已通知', 'T14231212', 'D45357', 'aa302'),
('ar303', '2026-06-10', '第三節課', '預約成功', '已通知', 'T14231212', 'D45368', 'aa303');

-- --------------------------------------------------------

--
-- 資料表結構 `availabletime`
--

CREATE TABLE `availabletime` (
  `c_id` char(6) NOT NULL,
  `at_id` char(6) NOT NULL,
  `at_start` datetime NOT NULL,
  `at_end` datetime NOT NULL,
  `at_state` enum('已被預約','尚未被預約') NOT NULL DEFAULT '尚未被預約',
  `m_id` char(10) DEFAULT NULL,
  `ar_id` char(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `availabletime`
--

INSERT INTO `availabletime` (`c_id`, `at_id`, `at_start`, `at_end`, `at_state`, `m_id`, `ar_id`) VALUES
('D45357', 'T00001', '2026-05-18 09:00:00', '2026-05-18 10:00:00', '尚未被預約', NULL, NULL),
('D45357', 'T00002', '2026-05-18 11:00:00', '2026-05-18 12:00:00', '已被預約', 'H34567890', 'ar001'),
('D45357', 'T00005', '2026-05-19 13:00:00', '2026-05-19 14:00:00', '尚未被預約', NULL, NULL),
('D45368', 'T00006', '2026-05-20 13:00:00', '2026-05-20 14:00:00', '已被預約', 'T14231212', 'ar301'),
('D45368', 'T00008', '2026-05-21 10:00:00', '2026-05-21 11:00:00', '尚未被預約', NULL, NULL),
('D45368', 'T00010', '2026-05-22 09:00:00', '2026-05-22 10:00:00', '已被預約', 'F41322154', 'ar065'),
('D12094', 'T00011', '2026-05-23 10:00:00', '2026-05-23 11:00:00', '尚未被預約', NULL, NULL),
('D12094', 'T00013', '2026-05-25 09:00:00', '2026-05-25 10:00:00', '尚未被預約', NULL, NULL),
('D12094', 'T00014', '2026-05-25 13:00:00', '2026-05-25 14:00:00', '已被預約', 'H34985632', 'ar002'),
('D22841', 'T00016', '2026-05-26 10:00:00', '2026-05-26 11:00:00', '已被預約', 'B12376587', 'ar051'),
('D22841', 'T00017', '2026-05-26 11:00:00', '2026-05-26 12:00:00', '已被預約', 'F41322154', 'ar052'),
('D22841', 'T00018', '2026-05-27 13:00:00', '2026-05-27 14:00:00', '已被預約', 'H34001234', 'ar053'),
('D77432', 'T00021', '2026-05-29 10:00:00', '2026-05-29 11:00:00', '已被預約', 'H34567890', 'ar054'),
('D77432', 'T00023', '2026-05-30 11:00:00', '2026-05-30 12:00:00', '已被預約', 'B12376587', 'ar055'),
('D77432', 'T00025', '2026-05-30 15:00:00', '2026-05-30 16:00:00', '已被預約', 'F41322154', 'ar056'),
('D12094', 'T00026', '2026-07-02 10:00:00', '2026-07-02 11:00:00', '尚未被預約', NULL, NULL),
('D12094', 'T00027', '2026-06-25 08:00:00', '2026-06-25 09:00:00', '尚未被預約', NULL, NULL),
('D12094', 'T00028', '2026-06-24 13:00:00', '2026-06-24 14:00:00', '尚未被預約', NULL, NULL),
('D12094', 'T00029', '2026-06-25 11:00:00', '2026-06-25 12:00:00', '尚未被預約', NULL, NULL),
('D12094', 'T00030', '2026-06-30 11:00:00', '2026-06-30 12:00:00', '尚未被預約', NULL, NULL),
('D12094', 'T00031', '2026-06-18 11:00:00', '2026-06-18 12:00:00', '尚未被預約', NULL, NULL),
('D12094', 'T00032', '2026-06-17 13:00:00', '2026-06-17 14:00:00', '尚未被預約', NULL, NULL),
('D45368', 'T00033', '2026-06-17 09:00:00', '2026-06-17 10:00:00', '已被預約', 'B12376587', 'ar064'),
('D45368', 'T00034', '2026-06-10 10:00:00', '2026-06-10 11:00:00', '已被預約', 'H34567890', 'ar063'),
('D45357', 'T00035', '2026-06-01 08:00:00', '2026-06-01 09:00:00', '已被預約', 'H34001234', 'ar057'),
('D45357', 'T00036', '2026-06-01 09:00:00', '2026-06-01 10:00:00', '已被預約', 'H34567890', 'ar058'),
('D45357', 'T00037', '2026-06-01 10:00:00', '2026-06-01 11:00:00', '已被預約', 'H34985632', 'ar059'),
('D45357', 'T00038', '2026-06-01 13:00:00', '2026-06-01 14:00:00', '已被預約', 'B12376587', 'ar060'),
('D45357', 'T00039', '2026-06-01 14:00:00', '2026-06-01 15:00:00', '尚未被預約', NULL, NULL),
('D77432', 'T00040', '2026-06-09 10:00:00', '2026-06-09 11:00:00', '已被預約', 'H34001234', 'ar062'),
('D77432', 'T00041', '2026-06-09 09:00:00', '2026-06-09 10:00:00', '已被預約', 'F41322154', 'ar061'),
('D12094', 'T00042', '2026-05-26 10:00:00', '2026-05-26 11:00:00', '尚未被預約', NULL, NULL),
('D12094', 'T00043', '2026-06-26 11:00:00', '2026-06-26 12:00:00', '尚未被預約', NULL, NULL),
('D12094', 'T00044', '2026-05-27 10:00:00', '2026-05-27 11:00:00', '尚未被預約', NULL, NULL),
('D12094', 'T00045', '2026-06-27 11:00:00', '2026-06-27 12:00:00', '尚未被預約', NULL, NULL),
('D12094', 'T00046', '2026-07-09 10:00:00', '2026-07-09 11:00:00', '尚未被預約', NULL, NULL),
('D12094', 'T00047', '2026-06-09 11:00:00', '2026-06-09 12:00:00', '尚未被預約', NULL, NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `blacklist`
--

CREATE TABLE `blacklist` (
  `bl_id` int(11) NOT NULL,
  `bl_reason` enum('無故爽約','頻繁遲到','騷擾與暴力行為','違反保密規定','惡意欠繳費用','違反醫療倫理之互動','其他') NOT NULL,
  `bl_start` datetime NOT NULL,
  `bl_end` datetime NOT NULL,
  `m_id` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `blacklist`
--

INSERT INTO `blacklist` (`bl_id`, `bl_reason`, `bl_start`, `bl_end`, `m_id`) VALUES
(1, '頻繁遲到', '2025-04-11 14:10:00', '2025-05-11 14:10:00', 'H34985632'),
(2, '騷擾與暴力行為', '2025-05-01 14:10:00', '2025-07-02 14:10:00', 'H34567890'),
(3, '騷擾與暴力行為', '2025-05-01 14:10:00', '2025-07-02 14:10:00', 'H34001234');

-- --------------------------------------------------------

--
-- 資料表結構 `consultant`
--

CREATE TABLE `consultant` (
  `c_id` char(6) NOT NULL,
  `c_name` varchar(10) NOT NULL,
  `c_gender` enum('女性','男性') NOT NULL,
  `c_mail` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `consultant`
--

INSERT INTO `consultant` (`c_id`, `c_name`, `c_gender`, `c_mail`) VALUES
('D12094', '陳志明', '男性', 'cming99@gmail.com'),
('D22841', '張雅婷', '女性', 'yating_chang@gmail.com'),
('D45357', '王政賢', '男性', 'chsien@gmail.com'),
('D45368', '林思妤', '女性', 'suyu.lin@gmail.com'),
('D77432', '賴冠宇', '男性', 'kuanyu.lai@gmail.com');

-- --------------------------------------------------------

--
-- 資料表結構 `consultant_field`
--

CREATE TABLE `consultant_field` (
  `c_id` char(6) NOT NULL,
  `c_field` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `consultant_field`
--

INSERT INTO `consultant_field` (`c_id`, `c_field`) VALUES
('D12094', '人際關係'),
('D12094', '自我探索'),
('D22841', '人際關係'),
('D22841', '學業壓力'),
('D45357', '人際關係'),
('D45357', '學業壓力'),
('D45368', '情緒困擾'),
('D45368', '自我探索'),
('D77432', '人際關係'),
('D77432', '情緒困擾'),
('D77432', '自我探索');

-- --------------------------------------------------------

--
-- 資料表結構 `consultant_tel`
--

CREATE TABLE `consultant_tel` (
  `c_id` char(6) NOT NULL,
  `c_tel` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `consultant_tel`
--

INSERT INTO `consultant_tel` (`c_id`, `c_tel`) VALUES
('D12094', '0966477823'),
('D22841', '06675843'),
('D45357', '06784002'),
('D45357', '0943432568'),
('D45368', '0977654143'),
('D77432', '03221453');

-- --------------------------------------------------------

--
-- 資料表結構 `consultationrecord`
--

CREATE TABLE `consultationrecord` (
  `cr_id` char(5) NOT NULL,
  `cr_start` datetime NOT NULL,
  `cr_end` datetime NOT NULL,
  `cr_duration` time NOT NULL,
  `cr_detail` text DEFAULT NULL,
  `ar_id` char(5) NOT NULL,
  `m_id` char(10) NOT NULL,
  `c_id` char(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `consultationrecord`
--

INSERT INTO `consultationrecord` (`cr_id`, `cr_start`, `cr_end`, `cr_duration`, `cr_detail`, `ar_id`, `m_id`, `c_id`) VALUES
('cr032', '2026-05-04 09:00:00', '2026-05-04 10:00:00', '01:00:00', '個案準時出席，態度配合，相談過程順暢。建議兩週後進行常態追蹤。', 'ar032', 'H34985632', 'D45357'),
('cr041', '2026-05-08 13:00:00', '2026-05-08 14:00:00', '01:00:00', '個案遲到約5分鐘，已完整補足諮商時數。本次主要進行情緒宣洩，狀況良好。', 'ar041', 'H34567890', 'D45368'),
('cr051', '2026-05-26 10:00:00', '2026-05-26 11:00:00', '01:00:00', '個案陳述人際衝突，引導其練習同理心溝通。', 'ar051', 'B12376587', 'D22841'),
('cr052', '2026-05-26 11:00:00', '2026-05-26 12:00:00', '01:00:00', '情感關係晤談，協助個案釐清自身核心需求。', 'ar052', 'F41322154', 'D22841'),
('cr053', '2026-05-27 13:00:00', '2026-05-27 14:00:00', '01:00:00', '期末課業壓力抒解，給予時間管理與行為微調建議。', 'ar053', 'H34001234', 'D22841'),
('cr054', '2026-05-29 10:00:00', '2026-05-29 11:00:00', '01:00:00', '轉系意願探索，分析個案興趣與現有科系之交集。', 'ar054', 'H34567890', 'D77432'),
('cr055', '2026-05-30 11:00:00', '2026-05-30 12:00:00', '01:00:00', '畢業生涯規劃，共同擬定履歷健診與實習計畫。', 'ar055', 'B12376587', 'D77432'),
('cr056', '2026-05-30 15:00:00', '2026-05-30 16:00:00', '01:00:00', '情緒低落支持性晤談，教導正念呼吸法緩解焦慮。', 'ar056', 'F41322154', 'D77432'),
('cr057', '2026-06-01 08:00:00', '2026-06-01 09:00:00', '01:00:00', '社交焦慮去敏感化練習，鼓勵校園人際小步嘗試。', 'ar057', 'H34001234', 'D45357'),
('cr058', '2026-06-01 09:00:00', '2026-06-01 10:00:00', '01:00:00', '孤單感調適，引導個案探索社團與課外興趣。', 'ar058', 'H34567890', 'D45357'),
('cr059', '2026-06-01 10:00:00', '2026-06-01 11:00:00', '01:00:00', '課業退學危機處理，擬定課後補救教學策略。', 'ar059', 'H34985632', 'D45357'),
('cr060', '2026-06-01 13:00:00', '2026-06-01 14:00:00', '01:00:00', '時間壓力爆表調適，重整生活作息與考前衝刺分配。', 'ar060', 'B12376587', 'D45357'),
('cr061', '2026-06-09 09:00:00', '2026-06-09 10:00:00', '01:00:00', '憤怒控制與情緒覺察，練習在衝突現場暫停倒數。', 'ar061', 'F41322154', 'D77432'),
('cr062', '2026-06-09 10:00:00', '2026-06-09 11:00:00', '01:00:00', '宿舍人際衝突，模擬與室友平心靜氣溝通之情境。', 'ar062', 'H34001234', 'D77432'),
('cr063', '2026-06-10 10:00:00', '2026-06-10 11:00:00', '01:00:00', '特質探索，運用心理評估工具回饋進行自我解讀。', 'ar063', 'H34567890', 'D45368'),
('cr064', '2026-06-17 09:00:00', '2026-06-17 10:00:00', '01:00:00', '就業與升學十字路口分析，條列利弊進行決策權衡。', 'ar064', 'B12376587', 'D45368'),
('cr065', '2026-06-17 13:00:00', '2026-06-17 14:00:00', '01:00:00', '期末考前急性焦慮因應，給予短期放鬆支持性晤談。', 'ar065', 'F41322154', 'D45368'),
('cr101', '2026-01-15 10:00:00', '2026-01-15 11:00:00', '01:00:00', '個案因室友關係深感困擾，晤談過程專注，已引導其思考溝通策略。', 'ar101', 'H34001234', 'D12094'),
('cr102', '2026-02-18 13:00:00', '2026-02-18 14:00:00', '01:00:00', '針對未來生涯規劃進行探索，個案特質適合分析類工作，建立初步行動計畫。', 'ar102', 'F41322154', 'D22841'),
('cr103', '2026-03-10 08:00:00', '2026-03-10 09:00:00', '01:00:00', '個案期末考壓力極大，本次諮商主要協助放鬆訓練，緩解失眠焦慮。', 'ar103', 'B12376587', 'D77432'),
('cr201', '2026-01-20 09:00:00', '2026-01-20 10:00:00', '01:00:00', '歷史追蹤：協助個案排解因微積分成績落後的挫折感，建立讀書小組計畫。', 'ar201', 'H34567890', 'D45357'),
('cr202', '2026-02-25 14:00:00', '2026-02-25 15:00:00', '01:00:00', '歷史追蹤：季節性情緒低落晤談，給予心理支持，個案表示心情有稍微抒解。', 'ar202', 'H34985632', 'D45368');

-- --------------------------------------------------------

--
-- 資料表結構 `member`
--

CREATE TABLE `member` (
  `m_id` char(10) NOT NULL,
  `m_password` char(10) NOT NULL,
  `m_name` varchar(20) NOT NULL,
  `m_email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `member`
--

INSERT INTO `member` (`m_id`, `m_password`, `m_name`, `m_email`) VALUES
('A12345', '222222', '郭富摔', 'a12345@gs.ncku.edu.tw'),
('A32165', '111111', '黎暗', 'a32165@gs.ncku.edu.tw'),
('A87564', '000000', '張學無', 'a87564@gs.ncku.edu.tw'),
('A95636', '987654', '劉的華', 'a95636@gs.ncku.edu.tw'),
('B12376587', '101010', '李小龍', 'B123765587@gmail.com'),
('E12345678', '123456', '王建紅', 'Wang@gmail.com'),
('F41322154', '888888', '黃小陳', 'F41322154@gmail.com'),
('H34001234', '654321', '陳小花', 'h34001234@gs.ncku.edu.tw'),
('H34567890', '111222', '王小明', 'h34567890@gs.ncku.edu.tw'),
('H34985632', '123456', '江小惠', 'h34985632@gs.ncku.edu.tw'),
('T14231212', '765432', '方小燦', 'T14231212@gmail.com');

-- --------------------------------------------------------

--
-- 資料表結構 `member_tel`
--

CREATE TABLE `member_tel` (
  `m_id` char(10) NOT NULL,
  `m_tel` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `member_tel`
--

INSERT INTO `member_tel` (`m_id`, `m_tel`) VALUES
('A12345', '03-3222222'),
('A32165', '06-2111111'),
('A87564', '02-1000000'),
('A95636', '0965987654'),
('B12376587', '0918232121'),
('E12345678', '0981238128'),
('F41322154', '0912837412'),
('H34001234', '0988-777-666'),
('H34567890', '02-3456-7890'),
('H34985632', '0911222333'),
('T14231212', '0933122312');

-- --------------------------------------------------------

--
-- 資料表結構 `resource`
--

CREATE TABLE `resource` (
  `r_id` char(10) NOT NULL,
  `r_ann` varchar(255) DEFAULT NULL,
  `r_service` text DEFAULT NULL,
  `r_ophour` varchar(100) DEFAULT NULL,
  `r_faq` text DEFAULT NULL,
  `r_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `resource`
--

INSERT INTO `resource` (`r_id`, `r_ann`, `r_service`, `r_ophour`, `r_faq`, `r_link`) VALUES
('a001', '心靈充電站 ：一場午後的邂逅', '各位同學辛苦啦~~隨著期中考週的結束，我們也推出了心靈充電站的活動，歡迎各位同學在4/30來諮商中心跟大家一起玩玩桌遊、吃吃點心，聊聊最近的生活，也能認識新的朋友~', '', '', ''),
('f001', '', '', '', 'Q1: 哪裡可以預約諮商？ A1: 在系統中找到「預約申請」填寫表單，預約成功後我們會將確切的資訊Email至您提供的信箱，請同學再多注意。', ''),
('l001', '台南市政府心理健康資源', '', '', '', 'https://health.tainan.gov.tw/list.asp?orcaid=7769BD73-BCC3-40FA-9516-5559C78E90F1');

-- --------------------------------------------------------

--
-- 資料表結構 `satisfaction`
--

CREATE TABLE `satisfaction` (
  `sat_id` char(10) NOT NULL,
  `sat_date` datetime NOT NULL,
  `ar_id` char(5) DEFAULT NULL,
  `m_id` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `satisfaction`
--

INSERT INTO `satisfaction` (`sat_id`, `sat_date`, `ar_id`, `m_id`) VALUES
('s001', '2026-05-11 14:10:00', 'ar041', 'H34567890'),
('s002', '2026-05-05 15:20:00', 'ar032', 'H34985632'),
('s051', '2026-05-27 14:30:00', 'ar051', 'B12376587'),
('s052', '2026-05-28 09:15:00', 'ar052', 'F41322154'),
('s053', '2026-05-29 11:00:00', 'ar053', 'H34001234'),
('s054', '2026-05-30 16:20:00', 'ar054', 'H34567890'),
('s055', '2026-06-01 10:00:00', 'ar055', 'B12376587'),
('s056', '2026-06-01 13:45:00', 'ar056', 'F41322154'),
('s057', '2026-06-02 09:30:00', 'ar057', 'H34001234'),
('s058', '2026-06-03 15:10:00', 'ar058', 'H34567890'),
('s060', '2026-06-02 14:50:00', 'ar060', 'B12376587'),
('s061', '2026-06-10 10:15:00', 'ar061', 'F41322154'),
('s062', '2026-06-11 11:40:00', 'ar062', 'H34001234'),
('s063', '2026-06-12 16:00:00', 'ar063', 'H34567890'),
('s064', '2026-06-18 13:20:00', 'ar064', 'B12376587'),
('s065', '2026-06-18 15:45:00', 'ar065', 'F41322154'),
('s101', '2026-05-12 10:00:00', 'ar101', 'H34001234'),
('s102', '2026-06-13 16:30:00', 'ar102', 'F41322154'),
('s103', '2026-06-16 11:00:00', 'ar103', 'B12376587'),
('s201', '2025-11-12 14:00:00', 'ar201', 'H34567890'),
('s202', '2025-12-07 09:30:00', 'ar202', 'H34985632');

-- --------------------------------------------------------

--
-- 資料表結構 `sat_survey`
--

CREATE TABLE `sat_survey` (
  `sat_id` char(10) NOT NULL,
  `sat_no` tinyint(4) NOT NULL,
  `sat_question` text NOT NULL,
  `sat_score` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `sat_survey`
--

INSERT INTO `sat_survey` (`sat_id`, `sat_no`, `sat_question`, `sat_score`) VALUES
('s001', 1, '您對本次服務是否滿意？', 5),
('s001', 2, '您是否願意再次使用本服務？', 4),
('s001', 3, '服務流程是否清楚？', 5),
('s001', 4, '服務人員態度是否良好？', 5),
('s001', 5, '整體環境是否舒適？', 3),
('s002', 1, '您對本次服務是否滿意？', 4),
('s002', 2, '您是否願意再次使用本服務？', 3),
('s002', 3, '服務流程是否清楚？', 5),
('s002', 4, '服務人員態度是否良好？', 4),
('s002', 5, '整體環境是否舒適？', 2),
('s003', 1, '您對本次服務是否滿意？', 5),
('s051', 1, '您對本次服務是否滿意？', 5),
('s051', 2, '您是否願意再次使用本服務？', 5),
('s051', 3, '服務流程是否清楚？', 5),
('s051', 4, '服務人員態度是否良好？', 4),
('s051', 5, '整體環境是否舒適？', 4),
('s052', 1, '您對本次服務是否滿意？', 4),
('s052', 2, '您是否願意再次使用本服務？', 4),
('s052', 3, '服務流程是否清楚？', 4),
('s052', 4, '服務人員態度是否良好？', 4),
('s052', 5, '整體環境是否舒適？', 4),
('s053', 1, '您對本次服務是否滿意？', 5),
('s053', 2, '您是否願意再次使用本服務？', 5),
('s053', 3, '服務流程是否清楚？', 4),
('s053', 4, '服務人員態度是否良好？', 5),
('s053', 5, '整體環境是否舒適？', 5),
('s054', 1, '您對本次服務是否滿意？', 5),
('s054', 2, '您是否願意再次使用本服務？', 5),
('s054', 3, '服務流程是否清楚？', 5),
('s054', 4, '服務人員態度是否良好？', 5),
('s054', 5, '整體環境是否舒適？', 5),
('s055', 1, '您對本次服務是否滿意？', 4),
('s055', 2, '您是否願意再次使用本服務？', 4),
('s055', 3, '服務流程是否清楚？', 5),
('s055', 4, '服務人員態度是否良好？', 4),
('s055', 5, '整體環境是否舒適？', 4),
('s056', 1, '您對本次服務是否滿意？', 5),
('s056', 2, '您是否願意再次使用本服務？', 5),
('s056', 3, '服務流程是否清楚？', 5),
('s056', 4, '服務人員態度是否良好？', 5),
('s056', 5, '整體環境是否舒適？', 5),
('s057', 1, '您對本次服務是否滿意？', 5),
('s057', 2, '您是否願意再次使用本服務？', 5),
('s057', 3, '服務流程是否清楚？', 4),
('s057', 4, '服務人員態度是否良好？', 4),
('s057', 5, '整體環境是否舒適？', 4),
('s058', 1, '您對本次服務是否滿意？', 4),
('s058', 2, '您是否願意再次使用本服務？', 4),
('s058', 3, '服務流程是否清楚？', 4),
('s058', 4, '服務人員態度是否良好？', 5),
('s058', 5, '整體環境是否舒適？', 4),
('s060', 1, '您對本次服務是否滿意？', 5),
('s060', 2, '您是否願意再次使用本服務？', 5),
('s060', 3, '服務流程是否清楚？', 5),
('s060', 4, '服務人員態度是否良好？', 5),
('s060', 5, '整體環境是否舒適？', 4),
('s061', 1, '您對本次服務是否滿意？', 5),
('s061', 2, '您是否願意再次使用本服務？', 5),
('s061', 3, '服務流程是否清楚？', 5),
('s061', 4, '服務人員態度是否良好？', 5),
('s061', 5, '整體環境是否舒適？', 5),
('s062', 1, '您對本次服務是否滿意？', 4),
('s062', 2, '您是否願意再次使用本服務？', 4),
('s062', 3, '服務流程是否清楚？', 4),
('s062', 4, '服務人員態度是否良好？', 4),
('s062', 5, '整體環境是否舒適？', 4),
('s063', 1, '您對本次服務是否滿意？', 5),
('s063', 2, '您是否願意再次使用本服務？', 5),
('s063', 3, '服務流程是否清楚？', 5),
('s063', 4, '服務人員態度是否良好？', 5),
('s063', 5, '整體環境是否舒適？', 5),
('s064', 1, '您對本次服務是否滿意？', 4),
('s064', 2, '您是否願意再次使用本服務？', 5),
('s064', 3, '服務流程是否清楚？', 4),
('s064', 4, '服務人員態度是否良好？', 4),
('s064', 5, '整體環境是否舒適？', 4),
('s065', 1, '您對本次服務是否滿意？', 5),
('s065', 2, '您是否願意再次使用本服務？', 4),
('s065', 3, '服務流程是否清楚？', 5),
('s065', 4, '服務人員態度是否良好？', 5),
('s065', 5, '整體環境是否舒適？', 5),
('s101', 1, '您對本次服務是否滿意？', 5),
('s101', 2, '您是否願意再次使用本服務？', 5),
('s101', 3, '服務流程是否清楚？', 5),
('s101', 4, '服務人員態度是否良好？', 5),
('s101', 5, '整體環境是否舒適？', 4),
('s102', 1, '您對本次服務是否滿意？', 4),
('s102', 2, '您是否願意再次使用本服務？', 4),
('s102', 3, '服務流程是否清楚？', 4),
('s102', 4, '服務人員態度是否良好：', 4),
('s102', 5, '整體環境是否舒適？', 3),
('s103', 1, '您對本次服務是否滿意？', 5),
('s103', 2, '您是否願意再次使用本服務？', 5),
('s103', 3, '服務流程是否清楚？', 5),
('s103', 4, '服務人員態度是否良好？', 5),
('s103', 5, '整體環境是否舒適？', 5),
('s201', 1, '您對本次服務是否滿意？', 5),
('s201', 2, '您是否願意再次使用本服務？', 5),
('s201', 3, '服務流程是否清楚？', 4),
('s201', 4, '服務人員態度是否良好？', 5),
('s201', 5, '整體環境是否舒適？', 4),
('s202', 1, '您對本次服務是否滿意？', 4),
('s202', 2, '您是否願意再次使用本服務？', 4),
('s202', 3, '服務流程是否清楚？', 5),
('s202', 4, '服務人員態度是否良好？', 4),
('s202', 5, '整體環境是否舒適？', 4);

-- --------------------------------------------------------

--
-- 資料表結構 `student`
--

CREATE TABLE `student` (
  `m_id` char(10) NOT NULL,
  `s_dept` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `student`
--

INSERT INTO `student` (`m_id`, `s_dept`) VALUES
('B12376587', '中文系'),
('E12345678', '歷史系'),
('F41322154', '機械系'),
('H34001234', '工資系'),
('H34567890', '工資系'),
('H34985632', '工資系'),
('T14231212', '會計系');

-- --------------------------------------------------------

--
-- 資料表結構 `student_emergency`
--

CREATE TABLE `student_emergency` (
  `m_id` char(10) NOT NULL,
  `s_emergency` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `student_emergency`
--

INSERT INTO `student_emergency` (`m_id`, `s_emergency`) VALUES
('H34001234', '0995-959-595'),
('H34567890', '119'),
('H34985632', '0999-995-995');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `administrative`
--
ALTER TABLE `administrative`
  ADD PRIMARY KEY (`m_id`);

--
-- 資料表索引 `appointmentapply`
--
ALTER TABLE `appointmentapply`
  ADD PRIMARY KEY (`aa_id`),
  ADD KEY `m_id` (`m_id`);

--
-- 資料表索引 `appointmentrecord`
--
ALTER TABLE `appointmentrecord`
  ADD PRIMARY KEY (`ar_id`),
  ADD KEY `m_id` (`m_id`),
  ADD KEY `c_id` (`c_id`),
  ADD KEY `aa_id` (`aa_id`);

--
-- 資料表索引 `availabletime`
--
ALTER TABLE `availabletime`
  ADD PRIMARY KEY (`at_id`),
  ADD KEY `m_id` (`m_id`),
  ADD KEY `c_id` (`c_id`),
  ADD KEY `ar_id` (`ar_id`);

--
-- 資料表索引 `blacklist`
--
ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`bl_id`),
  ADD KEY `m_id` (`m_id`);

--
-- 資料表索引 `consultant`
--
ALTER TABLE `consultant`
  ADD PRIMARY KEY (`c_id`);

--
-- 資料表索引 `consultant_field`
--
ALTER TABLE `consultant_field`
  ADD PRIMARY KEY (`c_id`,`c_field`);

--
-- 資料表索引 `consultant_tel`
--
ALTER TABLE `consultant_tel`
  ADD PRIMARY KEY (`c_id`,`c_tel`);

--
-- 資料表索引 `consultationrecord`
--
ALTER TABLE `consultationrecord`
  ADD PRIMARY KEY (`cr_id`),
  ADD KEY `m_id` (`m_id`),
  ADD KEY `ar_id` (`ar_id`),
  ADD KEY `c_id` (`c_id`);

--
-- 資料表索引 `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`m_id`);

--
-- 資料表索引 `member_tel`
--
ALTER TABLE `member_tel`
  ADD PRIMARY KEY (`m_id`,`m_tel`);

--
-- 資料表索引 `resource`
--
ALTER TABLE `resource`
  ADD PRIMARY KEY (`r_id`);

--
-- 資料表索引 `satisfaction`
--
ALTER TABLE `satisfaction`
  ADD PRIMARY KEY (`sat_id`),
  ADD KEY `m_id` (`m_id`),
  ADD KEY `fk_sat_appointment` (`ar_id`);

--
-- 資料表索引 `sat_survey`
--
ALTER TABLE `sat_survey`
  ADD PRIMARY KEY (`sat_id`,`sat_no`);

--
-- 資料表索引 `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`m_id`);

--
-- 資料表索引 `student_emergency`
--
ALTER TABLE `student_emergency`
  ADD PRIMARY KEY (`m_id`,`s_emergency`);

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `administrative`
--
ALTER TABLE `administrative`
  ADD CONSTRAINT `administrative_ibfk_1` FOREIGN KEY (`m_id`) REFERENCES `member` (`m_id`);

--
-- 資料表的限制式 `appointmentapply`
--
ALTER TABLE `appointmentapply`
  ADD CONSTRAINT `appointmentapply_ibfk_1` FOREIGN KEY (`m_id`) REFERENCES `student` (`m_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `appointmentrecord`
--
ALTER TABLE `appointmentrecord`
  ADD CONSTRAINT `appointmentrecord_ibfk_1` FOREIGN KEY (`m_id`) REFERENCES `student` (`m_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointmentrecord_ibfk_2` FOREIGN KEY (`c_id`) REFERENCES `consultant` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointmentrecord_ibfk_3` FOREIGN KEY (`aa_id`) REFERENCES `appointmentapply` (`aa_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `availabletime`
--
ALTER TABLE `availabletime`
  ADD CONSTRAINT `availabletime_ibfk_1` FOREIGN KEY (`m_id`) REFERENCES `student` (`m_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `availabletime_ibfk_2` FOREIGN KEY (`c_id`) REFERENCES `consultant` (`c_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `availabletime_ibfk_3` FOREIGN KEY (`ar_id`) REFERENCES `appointmentrecord` (`ar_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- 資料表的限制式 `blacklist`
--
ALTER TABLE `blacklist`
  ADD CONSTRAINT `blacklist_ibfk_1` FOREIGN KEY (`m_id`) REFERENCES `member` (`m_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `consultant_field`
--
ALTER TABLE `consultant_field`
  ADD CONSTRAINT `consultant_field_ibfk_1` FOREIGN KEY (`c_id`) REFERENCES `consultant` (`c_id`);

--
-- 資料表的限制式 `consultant_tel`
--
ALTER TABLE `consultant_tel`
  ADD CONSTRAINT `consultant_tel_ibfk_1` FOREIGN KEY (`c_id`) REFERENCES `consultant` (`c_id`);

--
-- 資料表的限制式 `consultationrecord`
--
ALTER TABLE `consultationrecord`
  ADD CONSTRAINT `consultationrecord_ibfk_1` FOREIGN KEY (`m_id`) REFERENCES `student` (`m_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `consultationrecord_ibfk_2` FOREIGN KEY (`ar_id`) REFERENCES `appointmentrecord` (`ar_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `consultationrecord_ibfk_3` FOREIGN KEY (`c_id`) REFERENCES `consultant` (`c_id`) ON UPDATE CASCADE;

--
-- 資料表的限制式 `member_tel`
--
ALTER TABLE `member_tel`
  ADD CONSTRAINT `member_tel_ibfk_1` FOREIGN KEY (`m_id`) REFERENCES `member` (`m_id`);

--
-- 資料表的限制式 `satisfaction`
--
ALTER TABLE `satisfaction`
  ADD CONSTRAINT `fk_sat_appointment` FOREIGN KEY (`ar_id`) REFERENCES `appointmentrecord` (`ar_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `satisfaction_ibfk_1` FOREIGN KEY (`m_id`) REFERENCES `member` (`m_id`);

--
-- 資料表的限制式 `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`m_id`) REFERENCES `member` (`m_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
