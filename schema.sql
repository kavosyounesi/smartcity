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
