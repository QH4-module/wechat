DROP TABLE IF EXISTS `tbl_wechat_mp_menu`;

CREATE TABLE IF NOT EXISTS `tbl_wechat_mp_menu`
(
    `id`          VARCHAR(64)   NOT NULL,
    `template_id` VARCHAR(64)   NOT NULL COMMENT '所属模板',
    `name`        VARCHAR(60)   NOT NULL COMMENT '菜单名称',
    `type`        VARCHAR(20)   NOT NULL COMMENT '菜单类型',
    `level`       TINYINT       NOT NULL COMMENT '菜单等级,只能是1和2',
    `parent_id`   VARCHAR(64)   NOT NULL COMMENT '上级菜单',
    `menu_key`    VARCHAR(128)  NOT NULL COMMENT '菜单key',
    `url`         VARCHAR(1024) NULL COMMENT '网页链接',
    `media_id`    VARCHAR(200)  NULL COMMENT '媒体ID',
    `appid`       VARCHAR(64)   NULL COMMENT '小程序appid',
    `pagepath`    VARCHAR(2000) NULL COMMENT '小程序页面路径',
    `create_time` BIGINT        NOT NULL,
    `del_time`    BIGINT        NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `parent_id_index` (`parent_id` ASC)
)
    ENGINE = InnoDB
    COMMENT = '微信-公众号-菜单';