-- =====================================================
--  پایگاه داده سامانه «لنده هوشمند»
--  این فایل را از طریق phpMyAdmin در دیتابیسی که در cPanel
--  ساخته‌اید ایمپورت کنید.
-- =====================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `news` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(500) NOT NULL,
  `category` VARCHAR(150) NOT NULL DEFAULT '',
  `excerpt` TEXT,
  `date` VARCHAR(50) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `market` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(500) NOT NULL,
  `price` VARCHAR(150) NOT NULL DEFAULT '',
  `category` VARCHAR(150) NOT NULL DEFAULT '',
  `phone` VARCHAR(30) NOT NULL DEFAULT '',
  `description` TEXT,
  `status` ENUM('pending','approved') NOT NULL DEFAULT 'pending',
  `date` VARCHAR(50) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `government` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(500) NOT NULL,
  `body` TEXT,
  `date` VARCHAR(50) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `municipality` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(500) NOT NULL,
  `body` TEXT,
  `date` VARCHAR(50) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `crisis_alerts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(500) NOT NULL,
  `level` VARCHAR(50) NOT NULL DEFAULT 'اطلاعیه',
  `body` TEXT,
  `active` TINYINT(1) NOT NULL DEFAULT 0,
  `date` VARCHAR(50) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `crisis_reports` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `type` VARCHAR(300) NOT NULL,
  `location` VARCHAR(300) NOT NULL DEFAULT '',
  `description` TEXT,
  `contact` VARCHAR(50) NOT NULL DEFAULT '',
  `status` VARCHAR(50) NOT NULL DEFAULT 'در انتظار بررسی',
  `date` VARCHAR(50) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tourism` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(500) NOT NULL,
  `category` VARCHAR(150) NOT NULL DEFAULT '',
  `description` TEXT,
  `date` VARCHAR(50) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(500) NOT NULL,
  `date` VARCHAR(150) NOT NULL DEFAULT '',
  `location` VARCHAR(300) NOT NULL DEFAULT '',
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(500) NOT NULL,
  `description` TEXT,
  `progress` INT NOT NULL DEFAULT 0,
  `status` VARCHAR(100) NOT NULL DEFAULT 'در حال اجرا',
  `location` VARCHAR(300) NOT NULL DEFAULT '',
  `requirements` TEXT,
  `obstacles` TEXT,
  `date` VARCHAR(50) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `polls` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `question` VARCHAR(500) NOT NULL,
  `options` TEXT NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `date` VARCHAR(50) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `poll_votes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `poll_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `option_index` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `one_vote_per_user` (`poll_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(200) NOT NULL,
  `email` VARCHAR(200) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
--  داده‌های نمونه اولیه (می‌توانید بعداً از پنل مدیریت ویرایش/حذف کنید)
-- =====================================================

INSERT INTO `news` (`title`, `category`, `excerpt`, `date`) VALUES
('آغاز جشنواره دشت شقایق‌های آل‌طیب لنده', 'گردشگری', 'هم‌زمان با فرارسیدن بهار، دشت‌های شقایق منطقه آل‌طیب میزبان گردشگران نوروزی می‌شود.', '۱۴۰۴/۰۱/۰۱'),
('آسفالت محور لنده - وحدت به بهره‌برداری رسید', 'عمرانی', 'این پروژه با هدف تسهیل دسترسی روستاهای حاشیه رودخانه جن انجام شد.', '۱۴۰۴/۰۱/۰۱'),
('تشکیل کارگروه پیشگیری از سیلاب در شهرستان لنده', 'مدیریت بحران', 'با نزدیک شدن به فصل بارش، کارگروه ویژه پایش رودخانه‌های جن و مارون تشکیل شد.', '۱۴۰۴/۰۱/۰۱'),
('ثبت‌نام دوره آموزش باغداری عناب و انار آغاز شد', 'کشاورزی', 'جهاد کشاورزی لنده دوره رایگان آموزش باغداران منطقه را برگزار می‌کند.', '۱۴۰۴/۰۱/۰۱');

INSERT INTO `market` (`title`, `price`, `category`, `phone`, `description`, `status`, `date`) VALUES
('عسل طبیعی کوهستانی لنده', '۴۵۰,۰۰۰ تومان / کیلو', 'محصولات کشاورزی', '09000000001', 'عسل خالص از زنبورستان‌های ییلاقی منطقه موگرمون.', 'approved', '۱۴۰۴/۰۱/۰۱'),
('گردوی خشک باغ تراب', '۲۸۰,۰۰۰ تومان / کیلو', 'محصولات کشاورزی', '09000000002', 'برداشت تازه، بدون واسطه از باغدار.', 'approved', '۱۴۰۴/۰۱/۰۱'),
('دست‌بافته‌های سنتی ایل طیبی', 'توافقی', 'صنایع‌دستی', '09000000003', 'گلیم و جاجیم بافت دست با نقوش محلی.', 'approved', '۱۴۰۴/۰۱/۰۱'),
('خدمات باغبانی و هرس درختان', 'از ۲۰۰,۰۰۰ تومان', 'خدمات', '09000000004', 'هرس، سم‌پاشی و نگهداری باغات عناب و گردو.', 'approved', '۱۴۰۴/۰۱/۰۱');

INSERT INTO `government` (`title`, `body`, `date`) VALUES
('اعلام برنامه ملاقات مردمی فرماندار', 'شهروندان گرامی می‌توانند روزهای دوشنبه هر هفته از ساعت ۹ تا ۱۲ بدون وقت قبلی به ملاقات فرماندار مراجعه کنند.', '۱۴۰۴/۰۱/۰۱'),
('برگزاری جلسه شورای اداری شهرستان', 'جلسه هماهنگی دستگاه‌های اجرایی با محوریت پیگیری پروژه‌های عمرانی برگزار شد.', '۱۴۰۴/۰۱/۰۱');

INSERT INTO `municipality` (`title`, `body`, `date`) VALUES
('تغییر ساعت جمع‌آوری پسماند شهری', 'از این هفته جمع‌آوری زباله محله‌های مرکزی شهر به ساعت ۲۱ تا ۲۳ منتقل می‌شود.', '۱۴۰۴/۰۱/۰۱'),
('آغاز طرح زیباسازی میدان مرکزی لنده', 'این طرح شامل کاشت فضای سبز و بهسازی نمای میدان مرکزی شهر است.', '۱۴۰۴/۰۱/۰۱');

INSERT INTO `crisis_alerts` (`title`, `level`, `body`, `active`, `date`) VALUES
('احتمال بارش‌های سیل‌آسا در ارتفاعات', 'هشدار', 'بر اساس اعلام هواشناسی، از ساکنان حاشیه رودخانه‌های جن و مارون درخواست می‌شود هوشیار باشند.', 1, '۱۴۰۴/۰۱/۰۱');

INSERT INTO `tourism` (`name`, `category`, `description`, `date`) VALUES
('چشمه موگرمون', 'چشمه', 'معروف به «بهشت گمشده»؛ چشمه‌ای همیشه‌جوشان با آب زلال در مسیر روستای وحدت، مقصد اصلی گردشگران نوروزی.', '۱۴۰۴/۰۱/۰۱'),
('شترسنگی لنده', 'طبیعی', 'صخره‌ای طبیعی به شکل شتر که آن را بزرگ‌ترین شترسنگی جهان می‌دانند.', '۱۴۰۴/۰۱/۰۱'),
('روستای عروه', 'روستا', 'روستایی با چشم‌انداز پلکانی شبیه ماسوله، در میان باغات عناب و گردو.', '۱۴۰۴/۰۱/۰۱'),
('کوه سیاه و سفید', 'طبیعی', 'ارتفاعاتی با دو رنگ متمایز سنگ که چشم‌اندازی کم‌نظیر برای طبیعت‌گردی ایجاد کرده‌اند.', '۱۴۰۴/۰۱/۰۱'),
('امامزاده سیدعمادالدین ولی', 'زیارتی', 'بستری برای گردشگری زیارتی در کنار طبیعت اطراف.', '۱۴۰۴/۰۱/۰۱'),
('دشت شقایق‌های آل‌طیب', 'دشت', 'در فصل بهار، این دشت‌ها مملو از گل‌های شقایق وحشی می‌شوند.', '۱۴۰۴/۰۱/۰۱');

INSERT INTO `events` (`title`, `date`, `location`, `description`) VALUES
('جشنواره دشت شقایق', 'اسفند تا فروردین', 'دشت آل‌طیب', 'جشنواره فصلی گردشگری هم‌زمان با شکوفایی شقایق‌های وحشی.'),
('نمایشگاه صنایع‌دستی ایل طیبی', 'تابستان', 'میدان مرکزی لنده', 'نمایش و فروش دست‌بافته‌ها و صنایع‌دستی محلی.');

INSERT INTO `projects` (`title`, `description`, `progress`, `status`, `location`, `requirements`, `obstacles`, `date`) VALUES
('بهسازی و آسفالت محور لنده - وحدت', 'این پروژه با هدف تسهیل دسترسی روستاهای حاشیه رودخانه جن و کاهش زمان تردد اجرا می‌شود.', 65, 'در حال اجرا', 'محور لنده - وحدت', 'تامین اعتبار تکمیلی از سفر استانی، تخصیص ماشین‌آلات راهسازی اضافی', 'تاخیر در تملک اراضی حریم راه، محدودیت اعتبارات عمرانی استان', '۱۴۰۴/۰۱/۰۱'),
('احداث مجتمع خدماتی - رفاهی میدان مرکزی', 'ساخت فضای چندمنظوره برای برگزاری بازارچه‌های محلی و رویدادهای فرهنگی.', 30, 'در حال اجرا', 'میدان مرکزی لنده', 'جذب سرمایه‌گذار بخش خصوصی، تامین مصالح ساختمانی', 'کمبود نقدینگی پیمانکار، توقف فصلی به دلیل شرایط جوی', '۱۴۰۴/۰۱/۰۱'),
('طرح آبرسانی پایدار روستای عروه', 'اصلاح و گسترش شبکه آبرسانی برای تامین آب شرب پایدار روستا.', 90, 'نزدیک به اتمام', 'روستای عروه', 'بازرسی نهایی و تحویل پروژه', '-', '۱۴۰۴/۰۱/۰۱');

INSERT INTO `polls` (`question`, `options`, `active`, `date`) VALUES
('کدام پروژه عمرانی برای شما در اولویت است؟', '["بهسازی معابر روستایی","توسعه فضای سبز شهری","تکمیل آبرسانی روستاها","ایجاد مراکز گردشگری"]', 1, '۱۴۰۴/۰۱/۰۱');

-- =====================================================
--  جداول جدید (نظرات، پیام‌ها، OTP، کارمندان)
-- =====================================================

CREATE TABLE IF NOT EXISTS `staff` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','governor','mayor') NOT NULL DEFAULT 'mayor',
  `name` VARCHAR(200) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `news_comments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `news_id` INT NOT NULL,
  `user_id` INT,
  `user_name` VARCHAR(200) NOT NULL DEFAULT 'کاربر ناشناس',
  `body` TEXT NOT NULL,
  `status` ENUM('pending','approved') NOT NULL DEFAULT 'pending',
  `reply` TEXT,
  `reply_by` VARCHAR(200),
  `reply_date` VARCHAR(50),
  `date` VARCHAR(50) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `from_user_id` INT,
  `from_name` VARCHAR(200) NOT NULL,
  `from_contact` VARCHAR(100) NOT NULL DEFAULT '',
  `to_role` ENUM('governor','mayor','department') NOT NULL DEFAULT 'governor',
  `department_name` VARCHAR(200) NOT NULL DEFAULT '',
  `subject` VARCHAR(500) NOT NULL,
  `body` TEXT NOT NULL,
  `reply` TEXT,
  `reply_by` VARCHAR(200),
  `reply_date` VARCHAR(50),
  `status` ENUM('new','replied','archived') NOT NULL DEFAULT 'new',
  `date` VARCHAR(50) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `otp_codes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `phone` VARCHAR(20) NOT NULL,
  `code` VARCHAR(10) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `used` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
--  حساب‌های پیش‌فرض کارمندان (حتماً قبل از راه‌اندازی رمز را تغییر دهید)
--  فرماندار: governor / lendeh-gov1404
--  شهردار: mayor / lendeh-mayor1404
-- =====================================================
INSERT IGNORE INTO `staff` (`username`, `password_hash`, `role`, `name`) VALUES
('governor', '$2b$12$DRWu7IuxDy046F.TqyB6pO2.PbENb4TEs8kDuZZNVeVpKesfj34Uy', 'governor', 'فرماندار شهرستان لنده'),
('mayor', '$2b$12$uG1nZUDEhQSydaUGpl10yuB.UPCJni/NGvCJB/Qnzi9..AtqaWOn.', 'mayor', 'شهردار لنده');
