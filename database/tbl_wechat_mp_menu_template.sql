DROP TABLE IF EXISTS `tbl_wechat_mp_menu_template`;

CREATE TABLE IF NOT EXISTS `tbl_wechat_mp_menu_template`
(
    `id`          VARCHAR(64)  NOT NULL COMMENT 'ID',
    `name`        VARCHAR(100) NOT NULL COMMENT '模板名称',
    `is_used`     TINYINT      NOT NULL COMMENT '是否正在使用',
    `create_time` BIGINT       NOT NULL COMMENT '创建时间',
    `del_time`    BIGINT       NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    COMMENT = '微信-公众号-菜单-模板';