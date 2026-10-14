-- MR multi-step workflow (A to G) migration for cms_db1.
-- Run once in phpMyAdmin / mysql client. Safe to re-run (IF NOT EXISTS / NOT EXISTS guards).

-- ---------------------------------------------------------------------------
-- 1. Step lookup
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS t_payment_step (
	StepId INT NOT NULL,
	StepName VARCHAR(100) NOT NULL,
	SortOrder INT NOT NULL DEFAULT 0,
	PRIMARY KEY (StepId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO t_payment_step (StepId, StepName, SortOrder) VALUES
	(1, 'Payment Receive', 1),
	(2, 'MR Verification', 2),
	(3, 'Collector Assignment', 3),
	(4, 'MR Collection Status', 4),
	(5, 'MR Collection Status Check', 5),
	(6, 'Allocate Invoice', 6),
	(7, 'Collection Verification', 7),
	(8, 'Completed', 8),
	(99, 'Rejected', 99)
ON DUPLICATE KEY UPDATE StepName = VALUES(StepName), SortOrder = VALUES(SortOrder);

-- ---------------------------------------------------------------------------
-- 2. Collection status lookup (Step D)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS t_collection_status (
	CollectionStatusId INT NOT NULL AUTO_INCREMENT,
	CollectionStatusName VARCHAR(100) NOT NULL,
	PRIMARY KEY (CollectionStatusId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO t_collection_status (CollectionStatusId, CollectionStatusName) VALUES
	(1, 'Collected'),
	(3, 'Not Collected')
ON DUPLICATE KEY UPDATE CollectionStatusName = VALUES(CollectionStatusName);

-- ---------------------------------------------------------------------------
-- 3. Approval history
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS t_payment_approval_log (
	LogId INT NOT NULL AUTO_INCREMENT,
	PaymentId INT NOT NULL,
	StepId INT NOT NULL,
	`Action` VARCHAR(20) NOT NULL,
	Comments VARCHAR(1000) NULL,
	UserId INT NULL,
	CreateTs TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (LogId),
	KEY IX_payment_approval_log_PaymentId (PaymentId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------------------------
-- 4. New columns on t_payment
-- ---------------------------------------------------------------------------
ALTER TABLE t_payment
	ADD COLUMN IF NOT EXISTS StepId INT NOT NULL DEFAULT 1,
	ADD COLUMN IF NOT EXISTS CollectorUserId INT NULL,
	ADD COLUMN IF NOT EXISTS CollectorAssignDate DATETIME NULL,
	ADD COLUMN IF NOT EXISTS CollectionStatusId INT NULL,
	ADD COLUMN IF NOT EXISTS CollectionDate DATE NULL,
	ADD COLUMN IF NOT EXISTS CollectedAmount DECIMAL(18,2) NULL,
	ADD COLUMN IF NOT EXISTS CollectionNote VARCHAR(500) NULL;

ALTER TABLE t_payment ADD INDEX IF NOT EXISTS IX_payment_StepId (StepId);

-- ---------------------------------------------------------------------------
-- 5. Backfill existing MRs: posted ones are completed, everything else starts at Step A
-- ---------------------------------------------------------------------------
UPDATE t_payment SET StepId = 8 WHERE StatusId = 5;
UPDATE t_payment SET StepId = 1 WHERE StatusId <> 5 OR StatusId IS NULL;

-- ---------------------------------------------------------------------------
-- 6. Menus (under the same parent as allocateinvoice), copying its level/type
-- ---------------------------------------------------------------------------
SET @tplParentId = (SELECT ParentId FROM t_menu WHERE MenuKey = 'allocateinvoice' LIMIT 1);
SET @tplMenuLevel = (SELECT MenuLevel FROM t_menu WHERE MenuKey = 'allocateinvoice' LIMIT 1);
SET @tplMenuType = (SELECT MenuType FROM t_menu WHERE MenuKey = 'allocateinvoice' LIMIT 1);
SET @tplSortOrder = (SELECT SortOrder FROM t_menu WHERE MenuKey = 'allocateinvoice' LIMIT 1);

SET @nextMenuId = (SELECT IFNULL(MAX(MenuId), 0) + 1 FROM t_menu);
INSERT INTO t_menu (MenuId, MenuKey, MenuTitle, Url, ParentId, MenuLevel, SortOrder, MenuType)
SELECT @nextMenuId, 'collectorassignment', 'Collector Assignment', '/collectorassignment', @tplParentId, @tplMenuLevel, @tplSortOrder, @tplMenuType
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM t_menu WHERE MenuKey = 'collectorassignment');

SET @nextMenuId = (SELECT IFNULL(MAX(MenuId), 0) + 1 FROM t_menu);
INSERT INTO t_menu (MenuId, MenuKey, MenuTitle, Url, ParentId, MenuLevel, SortOrder, MenuType)
SELECT @nextMenuId, 'mrcollectionstatus', 'MR Collection Status', '/mrcollectionstatus', @tplParentId, @tplMenuLevel, @tplSortOrder, @tplMenuType
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM t_menu WHERE MenuKey = 'mrcollectionstatus');

SET @nextMenuId = (SELECT IFNULL(MAX(MenuId), 0) + 1 FROM t_menu);
INSERT INTO t_menu (MenuId, MenuKey, MenuTitle, Url, ParentId, MenuLevel, SortOrder, MenuType)
SELECT @nextMenuId, 'mrcollectionstatuscheck', 'MR Collection Status Check', '/mrcollectionstatuscheck', @tplParentId, @tplMenuLevel, @tplSortOrder, @tplMenuType
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM t_menu WHERE MenuKey = 'mrcollectionstatuscheck');

SET @nextMenuId = (SELECT IFNULL(MAX(MenuId), 0) + 1 FROM t_menu);
INSERT INTO t_menu (MenuId, MenuKey, MenuTitle, Url, ParentId, MenuLevel, SortOrder, MenuType)
SELECT @nextMenuId, 'collectionverification', 'Collection Verification', '/collectionverification', @tplParentId, @tplMenuLevel, @tplSortOrder, @tplMenuType
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM t_menu WHERE MenuKey = 'collectionverification');

-- ---------------------------------------------------------------------------
-- 7. Permissions: give every role that has allocateinvoice the same access to the new menus.
--    Adjust afterwards in Role to Menu Permission if steps belong to different roles.
-- ---------------------------------------------------------------------------
INSERT INTO t_role_menu_map (ClientId, BranchId, RoleId, MenuId, PermissionType)
SELECT r.ClientId, r.BranchId, r.RoleId, n.MenuId, r.PermissionType
FROM t_role_menu_map r
INNER JOIN t_menu tpl ON tpl.MenuId = r.MenuId AND tpl.MenuKey = 'allocateinvoice'
INNER JOIN t_menu n ON n.MenuKey IN ('collectorassignment', 'mrcollectionstatus', 'mrcollectionstatuscheck', 'collectionverification')
WHERE NOT EXISTS (
	SELECT 1 FROM t_role_menu_map x
	WHERE x.ClientId = r.ClientId AND x.BranchId = r.BranchId AND x.RoleId = r.RoleId AND x.MenuId = n.MenuId
);
