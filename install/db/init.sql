ALTER TABLE  `prefix_user` ADD  `user_api_key` VARCHAR( 32 ) NULL ,
ADD UNIQUE (
`user_auth_key`
);