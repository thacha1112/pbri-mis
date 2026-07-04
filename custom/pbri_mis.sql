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

 Date: 04/07/2026 13:57:49
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fiscal_years
-- ----------------------------
INSERT INTO `fiscal_years` VALUES (1, 2569, 'active', 'ปีงบประมาณเริ่มต้นระบบแผนงาน', '2026-06-07 11:20:54', '2026-06-07 11:20:54');
INSERT INTO `fiscal_years` VALUES (2, 2570, 'active', 'ปีงบประมาณล่วงหน้า', '2026-06-07 11:20:54', '2026-06-07 11:20:54');

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
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of personnels
-- ----------------------------
INSERT INTO `personnels` VALUES (1, 'ธชา', 'ศรีนวลขาว', 'thacha@pi.ac.th', NULL, 8, NULL, 'active', '2026-06-07 06:35:38', '2026-06-07 06:35:38');

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
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_activity_budgets
-- ----------------------------
INSERT INTO `plan_activity_budgets` VALUES (9, 4, 36, 1000.00, '2026-06-19 11:11:22', '2026-06-19 11:11:22');
INSERT INTO `plan_activity_budgets` VALUES (13, 5, 36, 2000.00, '2026-06-22 01:16:52', '2026-06-22 01:16:52');
INSERT INTO `plan_activity_budgets` VALUES (14, 3, 35, 5000.00, '2026-06-22 01:51:10', '2026-06-22 01:51:10');
INSERT INTO `plan_activity_budgets` VALUES (15, 6, 43, 5000.00, '2026-06-22 06:28:20', '2026-06-22 06:28:20');

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
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_budget_sources
-- ----------------------------
INSERT INTO `plan_budget_sources` VALUES (2, 1, 'เงินงบประมาณ', 'active', '2026-06-07 13:08:14', '2026-06-07 13:08:14');
INSERT INTO `plan_budget_sources` VALUES (3, 1, 'เงินรายได้', 'active', '2026-06-07 13:08:39', '2026-06-07 13:08:39');

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
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_programs
-- ----------------------------
INSERT INTO `plan_programs` VALUES (1, 3, 'แผนงานบุคลากร', 'active', '2026-06-07 13:11:53', '2026-06-07 13:11:53');
INSERT INTO `plan_programs` VALUES (2, 3, 'แผนงานพื้นฐานด้านการพัฒนาและเสริมสร้างศักยภาพทรัพยากรมนุษย์', 'active', '2026-06-07 13:12:15', '2026-06-07 13:12:15');
INSERT INTO `plan_programs` VALUES (3, 3, 'แผนงานยุทธศาสตร์การพัฒนาสถาบันพระบรมราชชนก', 'active', '2026-06-07 13:12:31', '2026-06-07 13:12:31');

-- ----------------------------
-- Table structure for plan_project_activities
-- ----------------------------
DROP TABLE IF EXISTS `plan_project_activities`;
CREATE TABLE `plan_project_activities`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `objectives` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `indicators` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `target_group` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `outputs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `project_id`(`project_id` ASC) USING BTREE,
  CONSTRAINT `plan_project_activities_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `plan_projects` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_project_activities
-- ----------------------------
INSERT INTO `plan_project_activities` VALUES (3, 9, 'จัดหาครุภัณฑ์คอมพิวเตอร์', 'วัตถุประสงค์', 'ตัวชี้วัด', 'กลุ่มเป้าหมาย', 'ผลผลิต (Outputs)', '2026-06-15', '2026-06-22', '2026-06-19 07:53:16', '2026-06-19 07:53:16');
INSERT INTO `plan_project_activities` VALUES (4, 9, 'จัดหาค่าอาหารกลางวัน', 'อาหารกลางวัน', 'อาหารกลางวัน 2', 'อาหารกลางวัน 3', 'อาหารกลางวัน 4', '2026-06-22', '2026-06-29', '2026-06-19 08:17:16', '2026-06-19 08:17:16');
INSERT INTO `plan_project_activities` VALUES (5, 9, 'กิจกรรมที่ 3', NULL, NULL, NULL, NULL, '2026-06-21', '2026-06-26', '2026-06-22 01:16:52', '2026-06-22 01:16:52');
INSERT INTO `plan_project_activities` VALUES (6, 10, 'กิจกรรมที่ 1', NULL, NULL, NULL, NULL, '2026-06-22', '2026-06-26', '2026-06-22 06:28:20', '2026-06-22 06:28:20');

-- ----------------------------
-- Table structure for plan_project_budget_sources
-- ----------------------------
DROP TABLE IF EXISTS `plan_project_budget_sources`;
CREATE TABLE `plan_project_budget_sources`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` bigint UNSIGNED NOT NULL,
  `budget_source_id` bigint UNSIGNED NOT NULL,
  `program_id` bigint UNSIGNED NULL DEFAULT NULL,
  `category_id` bigint UNSIGNED NULL DEFAULT NULL,
  `allocated_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uq_project_source`(`project_id` ASC, `budget_source_id` ASC) USING BTREE,
  INDEX `budget_source_id`(`budget_source_id` ASC) USING BTREE,
  INDEX `fk_budget_program`(`program_id` ASC) USING BTREE,
  INDEX `fk_budget_category`(`category_id` ASC) USING BTREE,
  CONSTRAINT `fk_budget_category` FOREIGN KEY (`category_id`) REFERENCES `plan_budget_categories` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `fk_budget_program` FOREIGN KEY (`program_id`) REFERENCES `plan_programs` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `plan_project_budget_sources_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `plan_projects` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `plan_project_budget_sources_ibfk_2` FOREIGN KEY (`budget_source_id`) REFERENCES `plan_budget_sources` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 44 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_project_budget_sources
-- ----------------------------
INSERT INTO `plan_project_budget_sources` VALUES (35, 9, 2, NULL, NULL, 10000.00, '2026-06-19 10:57:13', '2026-06-19 10:57:13');
INSERT INTO `plan_project_budget_sources` VALUES (36, 9, 3, 2, 3, 6000.00, '2026-06-19 10:57:13', '2026-06-19 10:57:13');
INSERT INTO `plan_project_budget_sources` VALUES (43, 10, 2, NULL, NULL, 10000.00, '2026-06-22 06:27:53', '2026-06-22 06:27:53');

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
-- Table structure for plan_project_sub_activities
-- ----------------------------
DROP TABLE IF EXISTS `plan_project_sub_activities`;
CREATE TABLE `plan_project_sub_activities`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `activity_id`(`activity_id` ASC) USING BTREE,
  CONSTRAINT `plan_project_sub_activities_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `plan_project_activities` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_project_sub_activities
-- ----------------------------
INSERT INTO `plan_project_sub_activities` VALUES (2, 3, 'ซื้อ window 11', '2026-06-22 03:35:53', '2026-06-22 03:35:53');

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
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_projects
-- ----------------------------
INSERT INTO `plan_projects` VALUES (9, 'it001', 'เงินเดือน/ค่าจ้าง/ค่าตอบแทน', 1, 1, 8, 1, 1, 1, 5, 5, 15, 3, '2026-06-01', '2026-06-30', 'ความสำคัญ หลักการและเหตุผล *', 'วัตถุประสงค์ของโครงการ *', 'กลุ่มเป้าหมาย *', 'ตัวชี้วัดโครงการ', 'ผลผลิตโครงการ (Outputs)', 'pending', '2026-06-19 02:35:12', '2026-06-19 03:19:12');
INSERT INTO `plan_projects` VALUES (10, 'it002', '7.1ข(2) ผลลัพธ์ด้านความปลอดภัยและการเตรียมพร้อมต่อภาวะฉุกเฉิน', 1, 1, 8, 1, 2, 2, 1, 1, 1, 1, '2026-06-01', '2026-06-30', NULL, NULL, NULL, NULL, NULL, 'pending', '2026-06-22 06:27:24', '2026-06-22 06:27:40');

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
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_strategies
-- ----------------------------
INSERT INTO `plan_strategies` VALUES (1, 1, NULL, 'กลยุทธ์ 1.1 พัฒนาหลักสูตรการศึกษาด้านสุขภาพปฐมภูมิตามมาตรฐานสากล', 'active', '2026-06-07 07:27:57', '2026-06-07 07:27:57');
INSERT INTO `plan_strategies` VALUES (2, 1, '-', 'กลยุทธ์ 1.2 สร้าง/พัฒนารูปแบบการจัดการศึกษาด้านสุขภาพปฐมภูมิ', 'active', '2026-06-07 07:28:18', '2026-06-07 07:28:18');
INSERT INTO `plan_strategies` VALUES (3, 15, 'S4', '-', 'active', '2026-06-19 02:38:10', '2026-06-19 02:38:10');

-- ----------------------------
-- Table structure for plan_sub_activity_budgets
-- ----------------------------
DROP TABLE IF EXISTS `plan_sub_activity_budgets`;
CREATE TABLE `plan_sub_activity_budgets`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sub_activity_id` bigint UNSIGNED NOT NULL,
  `activity_budget_id` bigint UNSIGNED NOT NULL,
  `allocated_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `pr_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `pr_approved_date` date NULL DEFAULT NULL,
  `po_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `po_signed_date` date NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sub_activity_id`(`sub_activity_id` ASC) USING BTREE,
  INDEX `activity_budget_id`(`activity_budget_id` ASC) USING BTREE,
  CONSTRAINT `plan_sub_activity_budgets_ibfk_1` FOREIGN KEY (`sub_activity_id`) REFERENCES `plan_project_sub_activities` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `plan_sub_activity_budgets_ibfk_2` FOREIGN KEY (`activity_budget_id`) REFERENCES `plan_activity_budgets` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_sub_activity_budgets
-- ----------------------------
INSERT INTO `plan_sub_activity_budgets` VALUES (1, 2, 14, 3000.00, 0.00, NULL, 0.00, NULL, '2026-06-22 03:35:53', '2026-06-22 03:35:53');

-- ----------------------------
-- Table structure for plan_sub_activity_payments
-- ----------------------------
DROP TABLE IF EXISTS `plan_sub_activity_payments`;
CREATE TABLE `plan_sub_activity_payments`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sub_activity_budget_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `payment_date` date NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sub_activity_budget_id`(`sub_activity_budget_id` ASC) USING BTREE,
  CONSTRAINT `plan_sub_activity_payments_ibfk_1` FOREIGN KEY (`sub_activity_budget_id`) REFERENCES `plan_sub_activity_budgets` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of plan_sub_activity_payments
-- ----------------------------
INSERT INTO `plan_sub_activity_payments` VALUES (1, 1, 1111.00, '2026-06-22', NULL, 'pending', '2026-06-22 06:02:10', '2026-06-22 06:02:10');
INSERT INTO `plan_sub_activity_payments` VALUES (2, 1, 800.00, '2026-06-22', NULL, 'pending', '2026-06-22 06:05:57', '2026-06-22 06:05:57');
INSERT INTO `plan_sub_activity_payments` VALUES (3, 1, 500.00, '2026-06-22', NULL, 'pending', '2026-06-22 06:19:12', '2026-06-22 06:19:12');
INSERT INTO `plan_sub_activity_payments` VALUES (4, 1, 500.00, '2026-06-22', NULL, 'pending', '2026-06-22 06:20:04', '2026-06-22 06:20:04');
INSERT INTO `plan_sub_activity_payments` VALUES (5, 1, 80.00, '2026-06-22', NULL, 'pending', '2026-06-22 06:21:17', '2026-06-22 06:21:17');

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
INSERT INTO `sessions` VALUES ('77ZwK0sGPhm8ctW93ZF89YpE7QmsnWwIUxEkCzxK', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJPZUdRSFd3TjFLNDV6bDNVM2FtbkxZcU9HSHluTEo5bWRlWVA0R29wIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1783147977);

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
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 1, NULL, 'SQPtpcEyFH7Lo3oKDo10QBMKymdK6aakdz57O8pUgoYpWiGYSDehdIdCt26C', '2026-07-04 06:52:57', '2026-07-04 06:52:57');

-- ----------------------------
-- View structure for view_activity_budget_usage
-- ----------------------------
DROP VIEW IF EXISTS `view_activity_budget_usage`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_activity_budget_usage` AS select `a`.`id` AS `activity_id`,`ab`.`id` AS `activity_budget_id`,`ab`.`amount` AS `activity_allocated`,sum(`sab`.`allocated_amount`) AS `total_sub_allocated`,sum(`sap`.`amount`) AS `total_paid` from ((((`plan_project_activities` `a` join `plan_activity_budgets` `ab` on((`a`.`id` = `ab`.`activity_id`))) left join `plan_project_sub_activities` `sa` on((`a`.`id` = `sa`.`activity_id`))) left join `plan_sub_activity_budgets` `sab` on((`sa`.`id` = `sab`.`sub_activity_id`))) left join `plan_sub_activity_payments` `sap` on((`sab`.`id` = `sap`.`sub_activity_budget_id`))) group by `a`.`id`,`ab`.`id`;

SET FOREIGN_KEY_CHECKS = 1;
