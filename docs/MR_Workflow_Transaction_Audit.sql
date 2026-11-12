-- Transaction Audit (H) after Collection Verification (G).
-- StepId 8 becomes Transaction Audit and Completed moves to 9.
-- Run once in phpMyAdmin / mysql client BEFORE deploying the code.
-- Safe to re-run: step 1 only runs while t_payment_step has no StepId 9.

-- ---------------------------------------------------------------------------
-- 1. MRs that are already completed move from 8 to 9
-- ---------------------------------------------------------------------------
UPDATE t_payment SET StepId = 9
WHERE StepId = 8
	AND NOT EXISTS (SELECT 1 FROM t_payment_step WHERE StepId = 9);

-- ---------------------------------------------------------------------------
-- 2. Step lookup
-- ---------------------------------------------------------------------------
INSERT INTO t_payment_step (StepId, StepName, SortOrder) VALUES
	(8, 'Transaction Audit', 8),
	(9, 'Completed', 9)
ON DUPLICATE KEY UPDATE StepName = VALUES(StepName), SortOrder = VALUES(SortOrder);

-- ---------------------------------------------------------------------------
-- 3. Menu (same parent/level/type/sort as collectionverification)
-- ---------------------------------------------------------------------------
SET @tplParentId = (SELECT ParentId FROM t_menu WHERE MenuKey = 'collectionverification' LIMIT 1);
SET @tplMenuLevel = (SELECT MenuLevel FROM t_menu WHERE MenuKey = 'collectionverification' LIMIT 1);
SET @tplMenuType = (SELECT MenuType FROM t_menu WHERE MenuKey = 'collectionverification' LIMIT 1);
SET @tplSortOrder = (SELECT SortOrder FROM t_menu WHERE MenuKey = 'collectionverification' LIMIT 1);

SET @nextMenuId = (SELECT IFNULL(MAX(MenuId), 0) + 1 FROM t_menu);
INSERT INTO t_menu (MenuId, MenuKey, MenuTitle, Url, ParentId, MenuLevel, SortOrder, MenuType)
SELECT @nextMenuId, 'transactionaudit', 'Transaction Audit', '/transactionaudit', @tplParentId, @tplMenuLevel, @tplSortOrder, @tplMenuType
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM t_menu WHERE MenuKey = 'transactionaudit');

-- ---------------------------------------------------------------------------
-- 4. Permissions: every role that has collectionverification gets the same access.
--    Adjust afterwards in Role to Menu Permission if the audit belongs to a different role.
-- ---------------------------------------------------------------------------
INSERT INTO t_role_menu_map (ClientId, BranchId, RoleId, MenuId, PermissionType)
SELECT r.ClientId, r.BranchId, r.RoleId, n.MenuId, r.PermissionType
FROM t_role_menu_map r
INNER JOIN t_menu tpl ON tpl.MenuId = r.MenuId AND tpl.MenuKey = 'collectionverification'
INNER JOIN t_menu n ON n.MenuKey = 'transactionaudit'
WHERE NOT EXISTS (
	SELECT 1 FROM t_role_menu_map x
	WHERE x.ClientId = r.ClientId AND x.BranchId = r.BranchId AND x.RoleId = r.RoleId AND x.MenuId = n.MenuId
);
