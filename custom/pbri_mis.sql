/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 80407 (8.4.7)
 Source Host           : localhost:3306
 Source Schema         : pbri_mis

 Target Server Type    : MySQL
 Target Server Version : 80407 (8.4.7)
 File Encoding         : 65001

 Date: 24/07/2026 14:35:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for acquisition_methods
-- ----------------------------
DROP TABLE IF EXISTS `acquisition_methods`;
CREATE TABLE `acquisition_methods`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id วิธีการได้มา',
  `method_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'วิธีการได้มา',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of acquisition_methods
-- ----------------------------

-- ----------------------------
-- Table structure for asset_categories
-- ----------------------------
DROP TABLE IF EXISTS `asset_categories`;
CREATE TABLE `asset_categories`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ประเภทครุภัณฑ์',
  `category_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of asset_categories
-- ----------------------------

-- ----------------------------
-- Table structure for asset_items
-- ----------------------------
DROP TABLE IF EXISTS `asset_items`;
CREATE TABLE `asset_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id ครุภัณฑ์',
  `reference_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'เลขอ้างอิงภายในระบบ',
  `asset_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'หมายเลขครุภัณฑ์',
  `asset_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'ชื่อครุภัณฑ์',
  `gfmis_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'รหัสในระบบ GFMIS',
  `category_id` bigint UNSIGNED NOT NULL COMMENT 'ประเภทครุภัณฑ์ FK  อ้างอิงตาราง asset_categories',
  `department_id` bigint UNSIGNED NOT NULL COMMENT 'สำนัก/คณะ FK อ้างอิงตาราง departments',
  `division_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'หน่วยงานสังกัด FK  department อ้างอิงตาราง divisions',
  `specification` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'รายละเอียดครุภัณฑ์',
  `model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'ยี่ห้อ รุ่น',
  `serial_no_1` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'serial_no_1',
  `serial_no_2` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'serial_no_2',
  `contract_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'เลขที่สัญญาจัดซื้อ',
  `funding_type_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'ประเภทเงิน FK  อ้างอิงตาราง funding_types',
  `acquisition_method_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'วิธีการได้มา FK อ้างอิงตาราง acquisition_methods',
  `quantity` int NULL DEFAULT 1 COMMENT 'จำนวนครุภัณฑ์',
  `unit_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'หน่วยนับ FK อ้างอิงตาราง units',
  `unit_price` decimal(12, 2) NULL DEFAULT NULL COMMENT 'ราคาต่อหน่วย',
  `total_price` decimal(12, 2) NULL DEFAULT NULL COMMENT 'ราคาต่อชุด',
  `useful_life` int NULL DEFAULT NULL COMMENT 'อายุการใช้งาน / ปี',
  `depreciation_rate` decimal(5, 2) NULL DEFAULT NULL COMMENT 'อัตราเสื่อม % ต่อปี',
  `asset_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'รหัสตามทะเบียนครุภัณฑ์',
  `fiscal_year` year NULL DEFAULT NULL COMMENT 'ปีที่จัดซื้อ',
  `start_date` date NULL DEFAULT NULL COMMENT 'วันที่เริ่มใช้งาน ครุภัณฑ์',
  `expire_date` date NULL DEFAULT NULL COMMENT 'วันที่หมดอายุ ครุภัณฑ์',
  `vendor_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'ผู้ขาย FK อ้างอิงตาราง vendors',
  `location_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'สถานที่ตั้ง FK อ้างอิงตาราง locations',
  `status_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'สภาพการใช้งาน FK อ้างอิงตาราง asset_status',
  `usage_remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'หมายเหตุการใช้งาน รายละเอียดเพิ่มเติม',
  `disposal_remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'การจำหน่าย',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'หมายเหตุทั่วไป',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `reference_no`(`reference_no` ASC) USING BTREE,
  UNIQUE INDEX `asset_no`(`asset_no` ASC) USING BTREE,
  INDEX `fk_asset_category`(`category_id` ASC) USING BTREE,
  INDEX `fk_asset_department`(`department_id` ASC) USING BTREE,
  INDEX `fk_asset_division`(`division_id` ASC) USING BTREE,
  INDEX `fk_asset_funding`(`funding_type_id` ASC) USING BTREE,
  INDEX `fk_asset_method`(`acquisition_method_id` ASC) USING BTREE,
  INDEX `fk_asset_unit`(`unit_id` ASC) USING BTREE,
  INDEX `fk_asset_vendor`(`vendor_id` ASC) USING BTREE,
  INDEX `fk_asset_location`(`location_id` ASC) USING BTREE,
  INDEX `fk_asset_status`(`status_id` ASC) USING BTREE,
  CONSTRAINT `fk_asset_category` FOREIGN KEY (`category_id`) REFERENCES `asset_categories` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_asset_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_asset_division` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_asset_funding` FOREIGN KEY (`funding_type_id`) REFERENCES `funding_types` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_asset_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_asset_method` FOREIGN KEY (`acquisition_method_id`) REFERENCES `acquisition_methods` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_asset_status` FOREIGN KEY (`status_id`) REFERENCES `asset_status` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_asset_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_asset_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of asset_items
-- ----------------------------

-- ----------------------------
-- Table structure for asset_status
-- ----------------------------
DROP TABLE IF EXISTS `asset_status`;
CREATE TABLE `asset_status`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id สถานะ',
  `status_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'สถานะ = ใช้งานได้\r\n               ชำรุด\r\n               ซ่อมแซม\r\n               จำหน่าย',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of asset_status
-- ----------------------------

-- ----------------------------
-- Table structure for departments
-- ----------------------------
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED NULL DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `parent_id`(`parent_id` ASC) USING BTREE,
  CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of departments
-- ----------------------------
INSERT INTO `departments` VALUES (1, 'สำนักงานอธิการบดี', NULL, 'active', '2026-06-07 06:21:31', '2026-06-07 06:21:31');
INSERT INTO `departments` VALUES (2, 'สำนักงานสภาสถาบัน', 1, 'active', '2026-06-07 06:21:45', '2026-06-07 06:21:45');
INSERT INTO `departments` VALUES (3, 'กองกลาง', 1, 'active', '2026-06-07 06:22:09', '2026-06-07 06:22:09');
INSERT INTO `departments` VALUES (4, 'กองบริหารการคลังและพัสดุ', 1, 'active', '2026-06-07 06:22:28', '2026-06-07 06:22:28');
INSERT INTO `departments` VALUES (5, 'กองกฎหมาย', 1, 'active', '2026-06-07 06:22:42', '2026-06-07 06:22:42');
INSERT INTO `departments` VALUES (6, 'กองทรัพยากรบุคคล', 1, 'active', '2026-06-07 06:22:52', '2026-06-07 06:22:52');
INSERT INTO `departments` VALUES (7, 'กองยุทธศาสตร์และวิเทศสัมพันธ์', 1, 'active', '2026-06-07 06:23:01', '2026-06-07 06:23:01');
INSERT INTO `departments` VALUES (8, 'กองเทคโนโลยีดิจิทัล', 1, 'active', '2026-06-07 06:23:12', '2026-06-07 06:23:12');
INSERT INTO `departments` VALUES (9, 'กองกิจการนักศึกษา', 1, 'active', '2026-07-24 02:47:25', '2026-07-24 02:47:25');
INSERT INTO `departments` VALUES (10, 'สำนักวิชาการ', NULL, 'active', '2026-07-24 02:51:56', '2026-07-24 02:51:56');
INSERT INTO `departments` VALUES (11, 'กองส่งเสริมวิชาการและคุณภาพการศึกษา', 10, 'active', '2026-07-24 02:52:10', '2026-07-24 02:52:10');
INSERT INTO `departments` VALUES (12, 'กองทะเบียนและประมวลผล', 10, 'active', '2026-07-24 02:52:19', '2026-07-24 02:52:19');
INSERT INTO `departments` VALUES (13, 'กองวิจัยและพัฒนานวัตกรรม', 10, 'active', '2026-07-24 02:52:27', '2026-07-24 02:52:27');
INSERT INTO `departments` VALUES (14, 'กองบริการวิชาการ', 10, 'active', '2026-07-24 02:52:36', '2026-07-24 02:52:36');
INSERT INTO `departments` VALUES (15, 'สำนักงานบัณฑิตศึกษา', 10, 'active', '2026-07-24 02:52:46', '2026-07-24 02:52:46');
INSERT INTO `departments` VALUES (16, 'คณะสาธารณสุข', NULL, 'active', '2026-07-24 02:53:08', '2026-07-24 02:53:08');
INSERT INTO `departments` VALUES (17, 'คณะแพทยศาสตร์', NULL, 'active', '2026-07-24 02:53:20', '2026-07-24 02:53:20');
INSERT INTO `departments` VALUES (18, 'คณะพยาบาล', NULL, 'active', '2026-07-24 02:53:27', '2026-07-24 02:53:27');
INSERT INTO `departments` VALUES (19, 'ตรวจสอบภายใน', NULL, 'active', '2026-07-24 02:53:34', '2026-07-24 02:53:34');
INSERT INTO `departments` VALUES (20, 'คณะเภสัชศาสตร์', NULL, 'active', '2026-07-24 02:53:43', '2026-07-24 02:53:43');

-- ----------------------------
-- Table structure for fiscal_years
-- ----------------------------
DROP TABLE IF EXISTS `fiscal_years`;
CREATE TABLE `fiscal_years`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `year` int NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `fiscal_years_year_unique`(`year` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fiscal_years
-- ----------------------------
INSERT INTO `fiscal_years` VALUES (1, 2569, 'active', 'ปีงบประมาณเริ่มต้นระบบแผนงาน', '2026-06-07 11:20:54', '2026-06-07 11:20:54');
INSERT INTO `fiscal_years` VALUES (2, 2570, 'inactive', 'ปีงบประมาณล่วงหน้า', '2026-06-07 11:20:54', '2026-07-15 03:17:52');
INSERT INTO `fiscal_years` VALUES (4, 2568, 'active', NULL, '2026-07-12 12:15:53', '2026-07-12 12:15:53');

-- ----------------------------
-- Table structure for funding_types
-- ----------------------------
DROP TABLE IF EXISTS `funding_types`;
CREATE TABLE `funding_types`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id ประเภทเงิน',
  `funding_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'ชื่อประเภทเงิน',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of funding_types
-- ----------------------------

-- ----------------------------
-- Table structure for maintenance_logs
-- ----------------------------
DROP TABLE IF EXISTS `maintenance_logs`;
CREATE TABLE `maintenance_logs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id รายการซ่อมแซม',
  `asset_id` bigint UNSIGNED NOT NULL COMMENT 'ชื่อครุภัณฑ์ FK\n อ้างอิงครุภัณฑ์ asset_items',
  `maintenance_date` date NOT NULL COMMENT 'วันที่ดำเนินการซ่อมแซม',
  `maintenance_type` enum('maintenance','repair') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'repair' COMMENT 'บำรุงรักษา หรือ ซ่อมแซม',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'รายการซ่อม/บำรุง',
  `amount` decimal(12, 2) NULL DEFAULT 0.00 COMMENT 'ค่าใช้จ่าย',
  `vendor_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'บริษัท/ร้าน ซ่อมแซม  FK อ้างอิงตาราง vendors',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'หมายเหุต',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_maintenance_asset`(`asset_id` ASC) USING BTREE,
  INDEX `fk_maintenance_vendor`(`vendor_id` ASC) USING BTREE,
  CONSTRAINT `fk_maintenance_asset` FOREIGN KEY (`asset_id`) REFERENCES `asset_items` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_maintenance_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of maintenance_logs
-- ----------------------------

-- ----------------------------
-- Table structure for personnels
-- ----------------------------
DROP TABLE IF EXISTS `personnels`;
CREATE TABLE `personnels`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `emp_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `department_id` bigint UNSIGNED NULL DEFAULT NULL,
  `position_title` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `department_id`(`department_id` ASC) USING BTREE,
  CONSTRAINT `personnels_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of personnels
-- ----------------------------
INSERT INTO `personnels` VALUES (1, 'ธชา', 'ศรีนวลขาว', 'thacha@pi.ac.th', NULL, 8, NULL, 'active', '2026-06-07 06:35:38', '2026-07-09 04:13:09');
INSERT INTO `personnels` VALUES (2, 'ทดสอบ', 'ทดสอบ1', 'charat.pra@pi.ac.th', NULL, 5, NULL, 'active', '2026-07-09 08:30:33', '2026-07-12 06:13:00');
INSERT INTO `personnels` VALUES (4, 'จามจุรี', 'นพนภา', 'tttt@pi.ac.th', NULL, 3, NULL, 'active', '2026-07-09 08:44:03', '2026-07-09 08:44:03');

-- ----------------------------
-- Table structure for plan_activity_budgets
-- ----------------------------
DROP TABLE IF EXISTS `plan_activity_budgets`;
CREATE TABLE `plan_activity_budgets`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_id` bigint UNSIGNED NOT NULL,
  `project_budget_source_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `activity_id`(`activity_id` ASC) USING BTREE,
  INDEX `project_budget_source_id`(`project_budget_source_id` ASC) USING BTREE,
  CONSTRAINT `plan_activity_budgets_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `plan_project_activities` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `plan_activity_budgets_ibfk_2` FOREIGN KEY (`project_budget_source_id`) REFERENCES `plan_project_budget_sources` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 41 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_activity_budgets
-- ----------------------------
INSERT INTO `plan_activity_budgets` VALUES (31, 13, 51, 220000.00, '2026-07-22 13:52:37', '2026-07-22 13:52:37');
INSERT INTO `plan_activity_budgets` VALUES (32, 13, 50, 50000.00, '2026-07-22 13:52:37', '2026-07-22 13:52:37');
INSERT INTO `plan_activity_budgets` VALUES (33, 14, 51, 156000.00, '2026-07-22 14:10:09', '2026-07-22 14:10:09');
INSERT INTO `plan_activity_budgets` VALUES (34, 15, 51, 74000.00, '2026-07-22 14:10:52', '2026-07-22 14:10:52');
INSERT INTO `plan_activity_budgets` VALUES (38, 16, 56, 25000.00, '2026-07-23 13:42:47', '2026-07-23 13:42:47');
INSERT INTO `plan_activity_budgets` VALUES (39, 16, 54, 150000.00, '2026-07-23 13:42:47', '2026-07-23 13:42:47');
INSERT INTO `plan_activity_budgets` VALUES (40, 16, 55, 100000.00, '2026-07-23 13:42:47', '2026-07-23 13:42:47');

-- ----------------------------
-- Table structure for plan_activity_payments
-- ----------------------------
DROP TABLE IF EXISTS `plan_activity_payments`;
CREATE TABLE `plan_activity_payments`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_budget_id` bigint UNSIGNED NOT NULL COMMENT 'FK อ้างอิงเงินที่จัดสรรให้กิจกรรม',
  `payment_type` enum('transfer','borrow','payment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'payment' COMMENT 'โอน, ยืมเงิน, เบิกจ่ายจริง',
  `amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `payment_date` date NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `activity_budget_id`(`activity_budget_id` ASC) USING BTREE,
  CONSTRAINT `fk_activity_payments_budget` FOREIGN KEY (`activity_budget_id`) REFERENCES `plan_activity_budgets` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of plan_activity_payments
-- ----------------------------
INSERT INTO `plan_activity_payments` VALUES (1, 31, 'payment', 100000.00, '2026-07-31', 'เบิกจ่าย ครั้งที่ 1', '2026-07-23 13:13:58', '2026-07-23 13:13:58');
INSERT INTO `plan_activity_payments` VALUES (2, 31, 'payment', 100000.00, '2026-08-31', 'เบิกจ่ายครั้งที่ 2', '2026-07-23 13:31:15', '2026-07-23 13:31:15');
INSERT INTO `plan_activity_payments` VALUES (3, 31, 'payment', 5000.00, '2026-09-30', 'เบิกจ่ายครั้งสุดท้าย', '2026-07-23 13:31:48', '2026-07-23 13:31:48');
INSERT INTO `plan_activity_payments` VALUES (4, 32, 'borrow', 50000.00, '2026-10-31', 'ยืมเงิน', '2026-07-23 13:33:35', '2026-07-23 13:33:35');
INSERT INTO `plan_activity_payments` VALUES (5, 33, 'transfer', 156000.00, '2027-01-21', 'โอนเงินให้วิทยาลัย', '2026-07-23 13:34:27', '2026-07-23 13:34:27');
INSERT INTO `plan_activity_payments` VALUES (6, 38, 'payment', 25000.00, '2026-07-01', NULL, '2026-07-23 13:43:37', '2026-07-23 13:43:37');
INSERT INTO `plan_activity_payments` VALUES (7, 39, 'payment', 150000.00, '2026-07-22', NULL, '2026-07-23 13:43:55', '2026-07-23 13:43:55');
INSERT INTO `plan_activity_payments` VALUES (8, 31, 'payment', 5000.00, '2026-07-30', NULL, '2026-07-24 03:59:51', '2026-07-24 03:59:51');
INSERT INTO `plan_activity_payments` VALUES (9, 34, 'payment', 25000.00, '2026-08-11', NULL, '2026-07-24 04:02:57', '2026-07-24 04:02:57');

-- ----------------------------
-- Table structure for plan_budget_categories
-- ----------------------------
DROP TABLE IF EXISTS `plan_budget_categories`;
CREATE TABLE `plan_budget_categories`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `program_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `program_id`(`program_id` ASC) USING BTREE,
  CONSTRAINT `plan_budget_categories_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `plan_programs` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_budget_categories
-- ----------------------------
INSERT INTO `plan_budget_categories` VALUES (1, 1, 'งบบุคลากร', 'active', '2026-06-07 13:20:43', '2026-06-07 13:20:43');
INSERT INTO `plan_budget_categories` VALUES (2, 1, 'งบดำเนินงาน', 'active', '2026-06-07 13:21:18', '2026-06-07 13:21:18');
INSERT INTO `plan_budget_categories` VALUES (3, 2, 'งบดำเนินงาน', 'active', '2026-06-07 13:21:38', '2026-06-07 13:21:38');
INSERT INTO `plan_budget_categories` VALUES (4, 2, 'งบลงทุน', 'active', '2026-06-07 13:21:57', '2026-06-07 13:21:57');
INSERT INTO `plan_budget_categories` VALUES (5, 3, 'งบดำเนินงาน', 'active', '2026-06-07 13:22:12', '2026-06-07 13:22:12');
INSERT INTO `plan_budget_categories` VALUES (6, 3, 'งบลงทุน', 'active', '2026-06-07 13:22:24', '2026-06-07 13:22:24');
INSERT INTO `plan_budget_categories` VALUES (7, 3, 'งบจ่ายอื่น', 'active', '2026-06-07 13:22:35', '2026-06-07 13:22:35');
INSERT INTO `plan_budget_categories` VALUES (8, 4, 'งบลงทุน', 'active', '2026-07-12 12:30:16', '2026-07-12 12:31:38');
INSERT INTO `plan_budget_categories` VALUES (9, 4, 'งบจ่ายอื่น', 'active', '2026-07-12 12:31:24', '2026-07-12 12:31:24');
INSERT INTO `plan_budget_categories` VALUES (10, 4, 'งบดำเนินงาน', 'active', '2026-07-12 12:31:50', '2026-07-12 12:31:50');
INSERT INTO `plan_budget_categories` VALUES (11, 5, 'งบลงทุน', 'active', '2026-07-12 12:32:03', '2026-07-12 12:32:03');
INSERT INTO `plan_budget_categories` VALUES (12, 5, 'งบดำเนินงาน', 'active', '2026-07-12 12:32:18', '2026-07-12 12:32:18');
INSERT INTO `plan_budget_categories` VALUES (13, 6, 'งบดำเนินงาน', 'active', '2026-07-12 12:32:30', '2026-07-12 12:32:30');
INSERT INTO `plan_budget_categories` VALUES (14, 6, 'งบบุคลากร', 'active', '2026-07-12 12:32:47', '2026-07-12 12:32:47');
INSERT INTO `plan_budget_categories` VALUES (15, 7, 'งบจ่ายอื่น', 'active', '2026-07-12 12:33:31', '2026-07-12 12:33:31');
INSERT INTO `plan_budget_categories` VALUES (16, 7, 'งบลงทุน', 'active', '2026-07-12 12:33:45', '2026-07-12 12:33:45');
INSERT INTO `plan_budget_categories` VALUES (17, 7, 'งบดำเนินงาน', 'active', '2026-07-12 12:34:04', '2026-07-12 12:34:04');
INSERT INTO `plan_budget_categories` VALUES (18, 8, 'งบลงทุน', 'active', '2026-07-12 12:34:15', '2026-07-12 12:34:15');
INSERT INTO `plan_budget_categories` VALUES (19, 8, 'งบดำเนินงาน', 'active', '2026-07-12 12:34:29', '2026-07-12 12:34:29');
INSERT INTO `plan_budget_categories` VALUES (20, 9, 'งบดำเนินงาน', 'active', '2026-07-12 12:34:38', '2026-07-12 12:34:38');
INSERT INTO `plan_budget_categories` VALUES (21, 9, 'งบบุคลากร', 'active', '2026-07-12 12:34:46', '2026-07-12 12:34:46');

-- ----------------------------
-- Table structure for plan_budget_sources
-- ----------------------------
DROP TABLE IF EXISTS `plan_budget_sources`;
CREATE TABLE `plan_budget_sources`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_budget_sources_fiscal_year`(`fiscal_year_id` ASC) USING BTREE,
  CONSTRAINT `fk_budget_sources_fiscal_year` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_budget_sources
-- ----------------------------
INSERT INTO `plan_budget_sources` VALUES (2, 1, 'เงินงบประมาณ', 'active', '2026-06-07 13:08:14', '2026-06-07 13:08:14');
INSERT INTO `plan_budget_sources` VALUES (3, 1, 'เงินรายได้', 'active', '2026-06-07 13:08:39', '2026-06-07 13:08:39');
INSERT INTO `plan_budget_sources` VALUES (4, 4, 'เงินรายได้', 'active', '2026-07-12 12:16:14', '2026-07-19 08:20:27');
INSERT INTO `plan_budget_sources` VALUES (5, 4, 'เงินงบประมาณ', 'active', '2026-07-12 12:20:28', '2026-07-12 12:20:28');
INSERT INTO `plan_budget_sources` VALUES (6, 2, 'เงินงบประมาณ', 'active', '2026-07-12 12:20:59', '2026-07-12 12:20:59');
INSERT INTO `plan_budget_sources` VALUES (7, 2, 'เงินรายได้', 'active', '2026-07-12 12:21:07', '2026-07-12 12:21:07');
INSERT INTO `plan_budget_sources` VALUES (8, 2, 'เงินพิเศษ', 'active', '2026-07-13 02:26:24', '2026-07-13 02:26:24');

-- ----------------------------
-- Table structure for plan_construction_types
-- ----------------------------
DROP TABLE IF EXISTS `plan_construction_types`;
CREATE TABLE `plan_construction_types`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_construction_types
-- ----------------------------
INSERT INTO `plan_construction_types` VALUES (1, 'ไม่ใช่', 'active', NULL, NULL);
INSERT INTO `plan_construction_types` VALUES (2, 'ใช่', 'active', NULL, NULL);

-- ----------------------------
-- Table structure for plan_department_allocations
-- ----------------------------
DROP TABLE IF EXISTS `plan_department_allocations`;
CREATE TABLE `plan_department_allocations`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint UNSIGNED NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `source_fiscal_year_id` bigint UNSIGNED NOT NULL,
  `budget_source_id` bigint UNSIGNED NOT NULL,
  `program_id` bigint UNSIGNED NULL DEFAULT NULL,
  `category_id` bigint UNSIGNED NULL DEFAULT NULL,
  `total_amount` decimal(15, 2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unique_allocation`(`department_id` ASC, `fiscal_year_id` ASC, `budget_source_id` ASC, `program_id` ASC, `category_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of plan_department_allocations
-- ----------------------------
INSERT INTO `plan_department_allocations` VALUES (5, 5, 1, 4, 5, NULL, NULL, 500000.00, '2026-07-15 12:24:54', '2026-07-15 12:24:54');
INSERT INTO `plan_department_allocations` VALUES (6, 5, 1, 1, 3, 1, 1, 200000.00, '2026-07-15 12:29:37', '2026-07-15 12:29:37');
INSERT INTO `plan_department_allocations` VALUES (7, 5, 1, 1, 3, 1, 2, 500000.00, '2026-07-19 06:37:43', '2026-07-19 06:37:43');
INSERT INTO `plan_department_allocations` VALUES (8, 8, 1, 1, 2, NULL, NULL, 100000.00, '2026-07-19 06:38:20', '2026-07-19 06:38:20');
INSERT INTO `plan_department_allocations` VALUES (9, 8, 1, 1, 3, 2, 3, 350000.00, '2026-07-19 08:19:51', '2026-07-19 08:19:51');
INSERT INTO `plan_department_allocations` VALUES (10, 8, 1, 4, 4, 6, 14, 200000.00, '2026-07-19 08:20:55', '2026-07-19 08:21:06');

-- ----------------------------
-- Table structure for plan_goals
-- ----------------------------
DROP TABLE IF EXISTS `plan_goals`;
CREATE TABLE `plan_goals`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `strategic_issue_id` bigint UNSIGNED NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `strategic_issue_id`(`strategic_issue_id` ASC) USING BTREE,
  CONSTRAINT `plan_goals_ibfk_1` FOREIGN KEY (`strategic_issue_id`) REFERENCES `plan_strategic_issues` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_goals
-- ----------------------------
INSERT INTO `plan_goals` VALUES (1, 1, '-', 'เป้าประสงค์ 1 การจัดการศึกษาด้านสุขภาพปฐมภูมิเป็นที่ยอมรับในระดับสากล', 'active', '2026-06-07 07:22:01', '2026-06-07 07:22:01');
INSERT INTO `plan_goals` VALUES (2, 1, '-', 'เป้าประสงค์ 2 ผู้สำเร็จการศึกษาเป็นผู้นำด้านสุขภาพปฐมภูมิ', 'active', '2026-06-07 07:22:21', '2026-06-07 07:22:21');
INSERT INTO `plan_goals` VALUES (3, 2, NULL, 'เป้าประสงค์ 1 พัฒนาระบบสนับสนุนการวิจัยให้มีประสิทธิภาพ', 'active', '2026-06-07 07:22:34', '2026-06-07 07:22:34');
INSERT INTO `plan_goals` VALUES (4, 2, NULL, 'เป้าประสงค์ 2 การเป็นผู้นำระดับโลกในการวิจัยด้านระบบสุขภาพปฐมภูมิ', 'active', '2026-06-07 07:22:42', '2026-06-07 07:22:42');
INSERT INTO `plan_goals` VALUES (5, 3, '-', 'เป้าประสงค์ 1 เป็นศูนย์ความเป็นเลิศทางวิชาการที่ตอบสนองระบบสุขภาพปฐมภูมิ', 'active', '2026-06-07 07:22:55', '2026-06-07 07:22:55');
INSERT INTO `plan_goals` VALUES (6, 3, '-', 'เป้าประสงค์ 2 มีศูนย์บริการวิชาการด้านการดูแลสุขภาพปฐมภูมิที่บูรณาการพันธกิจอุดมศึกษา', 'active', '2026-06-07 07:23:08', '2026-06-07 07:23:08');
INSERT INTO `plan_goals` VALUES (7, 3, '-', 'เป้าประสงค์ 3 การสร้างเครือข่ายความร่วมมือสู่ความเป็นเลิศด้านสุขภาพปฐมภูมิในระดับชาติและนานาชาติ', 'active', '2026-06-07 07:23:22', '2026-06-07 07:23:22');
INSERT INTO `plan_goals` VALUES (8, 4, '-', 'เป้าประสงค์ 1 เป็นสถาบันที่มีระบบบริหารจัดการภายใต้องค์กรคุณธรรมและการพัฒนาที่ยั่งยืน (Sustainable Development Goals: SDGs)', 'active', '2026-06-07 07:23:44', '2026-06-07 07:23:44');
INSERT INTO `plan_goals` VALUES (9, 4, '-', 'เป้าประสงค์ 2 พัฒนาประสิทธิภาพของระบบบริหารจัดการด้านทรัพยากรบุคคล (Human Resource)', 'active', '2026-06-07 07:23:57', '2026-06-07 07:23:57');
INSERT INTO `plan_goals` VALUES (10, 4, '-', 'เป้าประสงค์ 3 การบริหารจัดการทางการเงิน', 'active', '2026-06-07 07:24:06', '2026-06-07 07:24:32');
INSERT INTO `plan_goals` VALUES (11, 4, '-', 'เป้าประสงค์ 4 การพัฒนามหาวิทยาลัยสู่การเป็น Smart University', 'active', '2026-06-07 07:24:19', '2026-06-07 07:24:19');
INSERT INTO `plan_goals` VALUES (12, 5, '-', 'เป้าประสงค์ 1 เป็นสถาบันที่มีระบบบริหารจัดการภายใต้องค์กรคุณธรรมและการพัฒนาที่ยั่งยืน (Sustainable Development Goals: SDGs)', 'active', '2026-06-07 07:25:15', '2026-06-07 07:25:15');
INSERT INTO `plan_goals` VALUES (13, 5, '-', 'เป้าประสงค์ 2 พัฒนาประสิทธิภาพของระบบบริหารจัดการด้านทรัพยากรบุคคล (Human Resource)', 'active', '2026-06-07 07:26:10', '2026-06-07 07:26:10');
INSERT INTO `plan_goals` VALUES (14, 5, '-', 'เป้าประสงค์ 3 การบริหารจัดการทางการเงิน', 'active', '2026-06-07 07:26:20', '2026-06-07 07:26:20');
INSERT INTO `plan_goals` VALUES (15, 5, '-', 'เป้าประสงค์ 4 การพัฒนามหาวิทยาลัยสู่การเป็น Smart University', 'active', '2026-06-07 07:26:30', '2026-06-07 07:26:30');

-- ----------------------------
-- Table structure for plan_missions
-- ----------------------------
DROP TABLE IF EXISTS `plan_missions`;
CREATE TABLE `plan_missions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fiscal_year_id`(`fiscal_year_id` ASC) USING BTREE,
  CONSTRAINT `plan_missions_ibfk_1` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_missions
-- ----------------------------
INSERT INTO `plan_missions` VALUES (1, 1, 'พันธกิจด้านการผลิตและพัฒนาบุคลากรที่เป็นเลิศ', 'active', '2026-06-07 07:16:45', '2026-06-07 07:16:45');
INSERT INTO `plan_missions` VALUES (2, 1, 'พันธกิจต้านการวิจัย นวัตกรรมและพัฒนาองค์ความรู้ค้านวิทยาศาสตร์', 'active', '2026-06-07 07:16:56', '2026-06-07 07:16:56');
INSERT INTO `plan_missions` VALUES (3, 1, 'พันธ์กิจด้านการบริการวิชาการและบริการด้านการแพทย์', 'active', '2026-06-07 07:17:05', '2026-06-07 07:17:05');
INSERT INTO `plan_missions` VALUES (4, 1, 'พันธกิจด้านการทำนุบำรุงศิลปะและวัฒนธรรม', 'active', '2026-06-07 07:17:14', '2026-06-07 07:17:14');
INSERT INTO `plan_missions` VALUES (5, 1, 'พันธ์กิจด้านการบริหารจัดการองค์กรภายใต้หลักธรรมาภิบาล', 'active', '2026-06-07 07:17:22', '2026-06-07 07:17:22');

-- ----------------------------
-- Table structure for plan_overseas_types
-- ----------------------------
DROP TABLE IF EXISTS `plan_overseas_types`;
CREATE TABLE `plan_overseas_types`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_overseas_types
-- ----------------------------
INSERT INTO `plan_overseas_types` VALUES (1, 'ไม่ใช่', 'active', NULL, NULL);
INSERT INTO `plan_overseas_types` VALUES (2, 'ใช่', 'active', NULL, NULL);

-- ----------------------------
-- Table structure for plan_programs
-- ----------------------------
DROP TABLE IF EXISTS `plan_programs`;
CREATE TABLE `plan_programs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `budget_source_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `budget_source_id`(`budget_source_id` ASC) USING BTREE,
  CONSTRAINT `plan_programs_ibfk_1` FOREIGN KEY (`budget_source_id`) REFERENCES `plan_budget_sources` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_programs
-- ----------------------------
INSERT INTO `plan_programs` VALUES (1, 3, 'แผนงานบุคลากร', 'active', '2026-06-07 13:11:53', '2026-06-07 13:11:53');
INSERT INTO `plan_programs` VALUES (2, 3, 'แผนงานพื้นฐานด้านการพัฒนาและเสริมสร้างศักยภาพทรัพยากรมนุษย์', 'active', '2026-06-07 13:12:15', '2026-06-07 13:12:15');
INSERT INTO `plan_programs` VALUES (3, 3, 'แผนงานยุทธศาสตร์การพัฒนาสถาบันพระบรมราชชนก', 'active', '2026-06-07 13:12:31', '2026-06-07 13:12:31');
INSERT INTO `plan_programs` VALUES (4, 4, 'แผนงานยุทธศาสตร์การพัฒนาสถาบันพระบรมราชชนก', 'active', '2026-07-12 12:21:50', '2026-07-12 12:21:50');
INSERT INTO `plan_programs` VALUES (5, 4, 'แผนงานพื้นฐานด้านการพัฒนาและเสริมสร้างศักยภาพทรัพยากรมนุษย์', 'active', '2026-07-12 12:22:13', '2026-07-12 12:22:13');
INSERT INTO `plan_programs` VALUES (6, 4, 'แผนงานบุคลากร', 'active', '2026-07-12 12:22:26', '2026-07-12 12:22:26');
INSERT INTO `plan_programs` VALUES (7, 7, 'แผนงานยุทธศาสตร์การพัฒนาสถาบันพระบรมราชชนก', 'active', '2026-07-12 12:25:51', '2026-07-12 12:25:51');
INSERT INTO `plan_programs` VALUES (8, 7, 'แผนงานพื้นฐานด้านการพัฒนาและเสริมสร้างศักยภาพทรัพยากรมนุษย์', 'active', '2026-07-12 12:26:08', '2026-07-12 12:26:08');
INSERT INTO `plan_programs` VALUES (9, 7, 'แผนงานบุคลากร', 'active', '2026-07-12 12:26:20', '2026-07-12 12:26:20');

-- ----------------------------
-- Table structure for plan_project_activities
-- ----------------------------
DROP TABLE IF EXISTS `plan_project_activities`;
CREATE TABLE `plan_project_activities`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `project_id`(`project_id` ASC) USING BTREE,
  CONSTRAINT `plan_project_activities_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `plan_projects` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_project_activities
-- ----------------------------
INSERT INTO `plan_project_activities` VALUES (9, 13, 'จัดหาระบบ บริหาร', '2026-07-01', '2026-07-31', '2026-07-19 07:18:01', '2026-07-19 07:21:01');
INSERT INTO `plan_project_activities` VALUES (11, 13, 'จัดหาระบบการศึกษา', '2026-07-01', '2026-07-31', '2026-07-19 07:21:21', '2026-07-19 07:21:21');
INSERT INTO `plan_project_activities` VALUES (13, 12, 'จัดประชุม ITA', '2026-07-01', '2026-07-31', '2026-07-22 13:52:28', '2026-07-22 13:52:28');
INSERT INTO `plan_project_activities` VALUES (14, 12, 'กิจกรรม ประชุมครั้งที่ 2', '2026-09-01', '2026-09-30', '2026-07-22 14:10:09', '2026-07-22 14:10:09');
INSERT INTO `plan_project_activities` VALUES (15, 12, 'จัดหาอาหาร', '2026-10-01', '2026-10-31', '2026-07-22 14:10:52', '2026-07-22 14:10:52');
INSERT INTO `plan_project_activities` VALUES (16, 14, 'กิจกรรมที่ 1', '2026-07-01', '2026-07-31', '2026-07-23 13:42:15', '2026-07-23 13:42:15');

-- ----------------------------
-- Table structure for plan_project_budget_sources
-- ----------------------------
DROP TABLE IF EXISTS `plan_project_budget_sources`;
CREATE TABLE `plan_project_budget_sources`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` bigint UNSIGNED NOT NULL,
  `budget_source_id` bigint UNSIGNED NOT NULL,
  `department_allocation_id` bigint UNSIGNED NULL DEFAULT NULL,
  `program_id` bigint UNSIGNED NULL DEFAULT NULL,
  `category_id` bigint UNSIGNED NULL DEFAULT NULL,
  `allocated_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uq_project_allocation`(`project_id` ASC, `department_allocation_id` ASC) USING BTREE,
  INDEX `budget_source_id`(`budget_source_id` ASC) USING BTREE,
  INDEX `fk_budget_program`(`program_id` ASC) USING BTREE,
  INDEX `fk_budget_category`(`category_id` ASC) USING BTREE,
  CONSTRAINT `fk_budget_category` FOREIGN KEY (`category_id`) REFERENCES `plan_budget_categories` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `fk_budget_program` FOREIGN KEY (`program_id`) REFERENCES `plan_programs` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `plan_project_budget_sources_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `plan_projects` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `plan_project_budget_sources_ibfk_2` FOREIGN KEY (`budget_source_id`) REFERENCES `plan_budget_sources` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 57 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_project_budget_sources
-- ----------------------------
INSERT INTO `plan_project_budget_sources` VALUES (50, 12, 3, 6, 1, 1, 50000.00, '2026-07-16 08:21:58', '2026-07-22 14:16:40');
INSERT INTO `plan_project_budget_sources` VALUES (51, 12, 5, 5, NULL, NULL, 450000.00, '2026-07-16 08:40:38', '2026-07-22 14:16:40');
INSERT INTO `plan_project_budget_sources` VALUES (52, 13, 2, 8, NULL, NULL, 85000.00, '2026-07-19 07:03:38', '2026-07-19 07:03:38');
INSERT INTO `plan_project_budget_sources` VALUES (54, 14, 3, 6, 1, 1, 150000.00, '2026-07-23 13:38:58', '2026-07-23 13:38:58');
INSERT INTO `plan_project_budget_sources` VALUES (55, 14, 3, 7, 1, 2, 100000.00, '2026-07-23 13:38:58', '2026-07-23 13:38:58');
INSERT INTO `plan_project_budget_sources` VALUES (56, 14, 5, 5, NULL, NULL, 25000.00, '2026-07-23 13:38:58', '2026-07-23 13:38:58');

-- ----------------------------
-- Table structure for plan_project_methods
-- ----------------------------
DROP TABLE IF EXISTS `plan_project_methods`;
CREATE TABLE `plan_project_methods`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_project_methods
-- ----------------------------
INSERT INTO `plan_project_methods` VALUES (1, 'ดำเนินการเอง', 'active', NULL, NULL);
INSERT INTO `plan_project_methods` VALUES (2, ' ดำเนินการเองและโอนให้วิทยาลัย', 'active', NULL, NULL);
INSERT INTO `plan_project_methods` VALUES (3, 'โอนวิทยาลัย', 'active', NULL, NULL);

-- ----------------------------
-- Table structure for plan_projects
-- ----------------------------
DROP TABLE IF EXISTS `plan_projects`;
CREATE TABLE `plan_projects`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `personnel_id` bigint UNSIGNED NOT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `project_method_id` bigint UNSIGNED NOT NULL,
  `construction_type_id` bigint UNSIGNED NOT NULL,
  `overseas_type_id` bigint UNSIGNED NOT NULL,
  `mission_id` bigint UNSIGNED NULL DEFAULT NULL,
  `strategic_issue_id` bigint UNSIGNED NULL DEFAULT NULL,
  `goal_id` bigint UNSIGNED NULL DEFAULT NULL,
  `strategy_id` bigint UNSIGNED NULL DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `background_rationale` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `objectives` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `target_group` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `indicators` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `outputs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `project_code`(`project_code` ASC) USING BTREE,
  INDEX `fiscal_year_id`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `personnel_id`(`personnel_id` ASC) USING BTREE,
  INDEX `department_id`(`department_id` ASC) USING BTREE,
  INDEX `project_method_id`(`project_method_id` ASC) USING BTREE,
  INDEX `construction_type_id`(`construction_type_id` ASC) USING BTREE,
  INDEX `overseas_type_id`(`overseas_type_id` ASC) USING BTREE,
  INDEX `mission_id`(`mission_id` ASC) USING BTREE,
  INDEX `strategic_issue_id`(`strategic_issue_id` ASC) USING BTREE,
  INDEX `goal_id`(`goal_id` ASC) USING BTREE,
  INDEX `strategy_id`(`strategy_id` ASC) USING BTREE,
  CONSTRAINT `plan_projects_ibfk_1` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `plan_projects_ibfk_10` FOREIGN KEY (`strategy_id`) REFERENCES `plan_strategies` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `plan_projects_ibfk_2` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `plan_projects_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `plan_projects_ibfk_4` FOREIGN KEY (`project_method_id`) REFERENCES `plan_project_methods` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `plan_projects_ibfk_5` FOREIGN KEY (`construction_type_id`) REFERENCES `plan_construction_types` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `plan_projects_ibfk_6` FOREIGN KEY (`overseas_type_id`) REFERENCES `plan_overseas_types` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `plan_projects_ibfk_7` FOREIGN KEY (`mission_id`) REFERENCES `plan_missions` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `plan_projects_ibfk_8` FOREIGN KEY (`strategic_issue_id`) REFERENCES `plan_strategic_issues` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `plan_projects_ibfk_9` FOREIGN KEY (`goal_id`) REFERENCES `plan_goals` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_projects
-- ----------------------------
INSERT INTO `plan_projects` VALUES (12, 'LAW-001', 'โครงการขับเคลื่อน ITA', 1, 2, 5, 1, 1, 1, NULL, NULL, NULL, NULL, '2026-07-01', '2026-07-31', '1. ความสำคัญ หลักการและเหตุผล *', '2. วัตถุประสงค์ของโครงการ *', '3. กลุ่มเป้าหมาย *', '4. ตัวชี้วัดโครงการ', '5. ผลผลิตโครงการ (Outputs)', 'completed', '2026-07-12 06:13:50', '2026-07-24 03:25:27');
INSERT INTO `plan_projects` VALUES (13, 'ท.3', 'พัฒนาระบบสนเทศ', 1, 1, 8, 1, 1, 1, NULL, NULL, NULL, NULL, '2026-07-01', '2026-07-31', NULL, NULL, NULL, NULL, NULL, 'pending', '2026-07-19 07:02:52', '2026-07-19 07:02:52');
INSERT INTO `plan_projects` VALUES (14, 'LAW-002', 'โครงการที่ 2', 1, 2, 5, 1, 1, 1, NULL, NULL, NULL, NULL, '2026-07-01', '2026-07-31', NULL, NULL, NULL, NULL, NULL, 'pending', '2026-07-23 13:38:06', '2026-07-23 13:38:06');

-- ----------------------------
-- Table structure for plan_strategic_issues
-- ----------------------------
DROP TABLE IF EXISTS `plan_strategic_issues`;
CREATE TABLE `plan_strategic_issues`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mission_id` bigint UNSIGNED NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `mission_id`(`mission_id` ASC) USING BTREE,
  CONSTRAINT `plan_strategic_issues_ibfk_1` FOREIGN KEY (`mission_id`) REFERENCES `plan_missions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_strategic_issues
-- ----------------------------
INSERT INTO `plan_strategic_issues` VALUES (1, 1, '-', 'ประเด็นยุทธศาสตร์ 1: การยกระดับการศึกษาด้านสุขภาพปฐมภูมิสู่ความเป็นเลิศ Excellence in Primary Care Education', 'active', '2026-06-07 07:19:05', '2026-06-07 07:19:05');
INSERT INTO `plan_strategic_issues` VALUES (2, 2, '-', 'ประเด็นยุทธศาสตร์ 2: ยกระดับการวิจัยและสร้างนวัตกรรมด้านสุขภาพปฐมภูมิสู่ความเป็นเลิศ Excellence in Primary Care Research and Innovation', 'active', '2026-06-07 07:19:39', '2026-06-07 07:19:39');
INSERT INTO `plan_strategic_issues` VALUES (3, 3, '-', 'ประเด็นยุทธศาสตร์ 3: เครือข่ายการบริการวิชาการด้านสุขภาพปฐมภูมิที่เป็นเลิศ Excellence in Primary Care Services and Network', 'active', '2026-06-07 07:19:49', '2026-06-07 07:21:08');
INSERT INTO `plan_strategic_issues` VALUES (4, 4, '-', 'ประเด็นยุทธศาสตร์ 4: การขับเคลื่อนระบบบริหารจัดการสู่การเป็นองค์กรสมรรถนะสูง Excellence in Organizational Management', 'active', '2026-06-07 07:20:12', '2026-06-07 07:21:19');
INSERT INTO `plan_strategic_issues` VALUES (5, 5, '-', 'ประเด็นยุทธศาสตร์ 5: การขับเคลื่อนระบบบริหารจัดการสู่การเป็นองค์กรสมรรถนะสูง Excellence in Organizational Management', 'active', '2026-06-07 07:20:23', '2026-06-07 07:21:31');

-- ----------------------------
-- Table structure for plan_strategies
-- ----------------------------
DROP TABLE IF EXISTS `plan_strategies`;
CREATE TABLE `plan_strategies`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `goal_id` bigint UNSIGNED NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `goal_id`(`goal_id` ASC) USING BTREE,
  CONSTRAINT `plan_strategies_ibfk_1` FOREIGN KEY (`goal_id`) REFERENCES `plan_goals` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_strategies
-- ----------------------------
INSERT INTO `plan_strategies` VALUES (1, 1, NULL, 'กลยุทธ์ 1.1 พัฒนาหลักสูตรการศึกษาด้านสุขภาพปฐมภูมิตามมาตรฐานสากล', 'active', '2026-06-07 07:27:57', '2026-06-07 07:27:57');
INSERT INTO `plan_strategies` VALUES (2, 1, '-', 'กลยุทธ์ 1.2 สร้าง/พัฒนารูปแบบการจัดการศึกษาด้านสุขภาพปฐมภูมิ', 'active', '2026-06-07 07:28:18', '2026-06-07 07:28:18');
INSERT INTO `plan_strategies` VALUES (3, 15, 'S4', '-', 'active', '2026-06-19 02:38:10', '2026-06-19 02:38:10');
INSERT INTO `plan_strategies` VALUES (4, 4, 'KPI 7.1ก-01', 'c', 'active', '2026-07-12 07:12:19', '2026-07-12 07:12:19');

-- ----------------------------
-- Table structure for project_strategies
-- ----------------------------
DROP TABLE IF EXISTS `project_strategies`;
CREATE TABLE `project_strategies`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` bigint UNSIGNED NOT NULL,
  `mission_id` bigint UNSIGNED NOT NULL,
  `strategic_issue_id` bigint UNSIGNED NOT NULL,
  `goal_id` bigint UNSIGNED NOT NULL,
  `strategy_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_ps_project`(`project_id` ASC) USING BTREE,
  INDEX `fk_ps_mission`(`mission_id` ASC) USING BTREE,
  INDEX `fk_ps_strategic_issue`(`strategic_issue_id` ASC) USING BTREE,
  INDEX `fk_ps_goal`(`goal_id` ASC) USING BTREE,
  INDEX `fk_ps_strategy`(`strategy_id` ASC) USING BTREE,
  CONSTRAINT `fk_ps_goal` FOREIGN KEY (`goal_id`) REFERENCES `plan_goals` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_ps_mission` FOREIGN KEY (`mission_id`) REFERENCES `plan_missions` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_ps_project` FOREIGN KEY (`project_id`) REFERENCES `plan_projects` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_ps_strategic_issue` FOREIGN KEY (`strategic_issue_id`) REFERENCES `plan_strategic_issues` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_ps_strategy` FOREIGN KEY (`strategy_id`) REFERENCES `plan_strategies` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of project_strategies
-- ----------------------------
INSERT INTO `project_strategies` VALUES (8, 12, 1, 1, 1, 1, '2026-07-12 07:48:08', '2026-07-12 07:48:08');
INSERT INTO `project_strategies` VALUES (9, 12, 5, 5, 15, 3, '2026-07-12 07:48:08', '2026-07-12 07:48:08');
INSERT INTO `project_strategies` VALUES (10, 13, 2, 2, 4, 4, '2026-07-19 07:02:57', '2026-07-19 07:02:57');
INSERT INTO `project_strategies` VALUES (11, 13, 5, 5, 15, 3, '2026-07-19 07:02:57', '2026-07-19 07:02:57');
INSERT INTO `project_strategies` VALUES (12, 14, 1, 1, 1, 1, '2026-07-23 13:38:12', '2026-07-23 13:38:12');
INSERT INTO `project_strategies` VALUES (13, 14, 1, 1, 1, 2, '2026-07-23 13:38:12', '2026-07-23 13:38:12');

-- ----------------------------
-- Table structure for role_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions`  (
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of role_has_permissions
-- ----------------------------

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, 'admin', 'Super admin', 'ดูแลสิทธิ์ทุกอย่าง', '2026-07-09 04:36:45', '2026-07-10 10:30:26');
INSERT INTO `roles` VALUES (2, 'เจ้าหน้าที่งานแผน', 'เจ้าหน้าที่งานแผน', NULL, '2026-07-09 04:37:09', '2026-07-09 04:37:09');
INSERT INTO `roles` VALUES (3, 'เจ้าหน้าที่งานพัสดุ', 'เจ้าหน้าที่งานพัสดุ', NULL, '2026-07-09 04:37:29', '2026-07-09 04:37:29');
INSERT INTO `roles` VALUES (4, 'ผู้ใช้งานทั่วไป (งานแผน)', 'ผู้ใช้งานทั่วไป (งานแผน)', NULL, '2026-07-09 04:37:53', '2026-07-09 04:37:53');
INSERT INTO `roles` VALUES (5, 'ผู้ใช้งานทั่วไป (งานพัสดุ)', 'ผู้ใช้งานทั่วไป (งานพัสดุ)', NULL, '2026-07-09 04:38:10', '2026-07-09 04:38:10');
INSERT INTO `roles` VALUES (6, 'เจ้าหน้าที่งานบุคลากร', 'เจ้าหน้าที่งานบุคลากร', NULL, '2026-07-10 04:12:28', '2026-07-10 04:12:28');

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of sessions
-- ----------------------------
INSERT INTO `sessions` VALUES ('Hgfsv84grUlT5oosVeMJRpTwAoqIadZbZNn26fHy', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJmdlRKRTBmbjJWSjREZllVTHBlejRTNnRIdzFXNk56OFR1ald0T3IyIiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvcGxhblwvcHJvamVjdHNcLzEyXC9lZGl0P3RhYj1nZW5lcmFsIiwicm91dGUiOiJwbGFuLnByb2plY3RzLmVkaXQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1784866555);
INSERT INTO `sessions` VALUES ('ORvK48tmBQB9FScrksBpSCxOYqohlnY89iVTEUUK', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJQNGVQc241RUU5SXc4MzcySXlXVTlwc09WSjBpMlpJVE5sRUROeFRCIiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1784861142);
INSERT INTO `sessions` VALUES ('Shtf1vT040dIWHMgt0wmKn2xefSthN8JBoJno9kW', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJsc1NVdzhTM0N1aW1FZG9HcDBQdUh2dnRxb05OaWxaVktBOTZKOXA1IiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvcGxhblwvcmVwb3J0c1wvcHJvamVjdC1zdW1tYXJ5XC9leHBvcnQtZXhjZWwiLCJyb3V0ZSI6InBsYW4ucmVwb3J0cy5wcm9qZWN0X3N1bW1hcnkuZXhwb3J0LmV4Y2VsIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1784877510);
INSERT INTO `sessions` VALUES ('WvBCgb39zNvUsIfYt8udkA0ERDivzcMZ4nWwUzAG', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJaR2ZYTEx0OG54Z3NndUNtZEZmOG9uQU93OE04YjdUaVV3MUR3ZWVZIiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvcGxhblwvcmVwb3J0c1wvcHJvamVjdC1zdW1tYXJ5P2Zpc2NhbF95ZWFyX2lkPTEmcGFyZW50X2RlcGFydG1lbnRfaWQ9MSIsInJvdXRlIjoicGxhbi5yZXBvcnRzLnByb2plY3Rfc3VtbWFyeSJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1784868012);

-- ----------------------------
-- Table structure for units
-- ----------------------------
DROP TABLE IF EXISTS `units`;
CREATE TABLE `units`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id หน่วยนับ',
  `unit_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'หน่วยนับ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of units
-- ----------------------------

-- ----------------------------
-- Table structure for user_has_roles
-- ----------------------------
DROP TABLE IF EXISTS `user_has_roles`;
CREATE TABLE `user_has_roles`  (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of user_has_roles
-- ----------------------------
INSERT INTO `user_has_roles` VALUES (1, 1);
INSERT INTO `user_has_roles` VALUES (1, 4);
INSERT INTO `user_has_roles` VALUES (1, 6);
INSERT INTO `user_has_roles` VALUES (2, 4);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `personnals_id` bigint UNSIGNED NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `personnals_id`(`personnals_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 1, NULL, 'mjl4LWasdOn9uCAFrJCvnXMriI2TIMP0zbXH7Eyij6Sxpp4pyzcvh18vejtx', '2026-07-04 06:52:57', '2026-07-06 07:08:47');
INSERT INTO `users` VALUES (2, 2, NULL, 'UuY5IOoCKezV3yG0rTCyZts7dS64IZJMvJxOa4451901r9KmVKDG9mGDrevM', '2026-07-09 08:37:21', '2026-07-09 08:37:21');
INSERT INTO `users` VALUES (3, 4, NULL, NULL, '2026-07-09 08:44:03', '2026-07-09 08:44:03');

-- ----------------------------
-- Table structure for vendors
-- ----------------------------
DROP TABLE IF EXISTS `vendors`;
CREATE TABLE `vendors`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id บริษัท-ผู้ขาย',
  `vendor_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'ชื่อ บริษัท-ผู้ขาย',
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'ที่อยู่',
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'โทรศัพท์',
  `tax_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of vendors
-- ----------------------------

-- ----------------------------
-- View structure for view_activity_budget_usage
-- ----------------------------
DROP VIEW IF EXISTS `view_activity_budget_usage`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_activity_budget_usage` AS select `a`.`id` AS `activity_id`,`ab`.`id` AS `activity_budget_id`,`ab`.`amount` AS `activity_allocated`,sum(`sab`.`allocated_amount`) AS `total_sub_allocated`,sum(`sap`.`amount`) AS `total_paid` from ((((`plan_project_activities` `a` join `plan_activity_budgets` `ab` on((`a`.`id` = `ab`.`activity_id`))) left join `plan_project_sub_activities` `sa` on((`a`.`id` = `sa`.`activity_id`))) left join `plan_sub_activity_budgets` `sab` on((`sa`.`id` = `sab`.`sub_activity_id`))) left join `plan_sub_activity_payments` `sap` on((`sab`.`id` = `sap`.`sub_activity_budget_id`))) group by `a`.`id`,`ab`.`id`;

SET FOREIGN_KEY_CHECKS = 1;
