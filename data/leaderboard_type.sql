INSERT INTO `clubmetro`.`reward_leaderboard_type` (
	`id` ,
	`name` ,
	`created_at` ,
	`updated_at`
)
VALUES (
	1, 'Tous', NOW(), NOW() 
), (
	2, 'Participations', NOW(), NOW() 
), (
	3, 'Parrainages', NOW(), NOW() 
), (
	4, 'Partages', NOW(), NOW() 
);

INSERT INTO `clubmetro`.`reward_action_leaderboard_type` (
	`leaderboardtype_id`,
	`action_id`
)
VALUES (
	1, 1
), (
	1, 2
), (
	1, 3
), (
	1, 4
), (
	1, 5
), (
	1, 6
), (
	1, 7
), (
	1, 8
), (
	1, 9
), (
	1, 10
), (
	1, 11
), (
	1, 12
), (
	1, 13
), (
	1, 14
), (
	1, 15
), (
	1, 16
), (
	1, 17
), (
	1, 20
), (
	1, 25
), (
	1, 30
), (
	1, 100
), (
	1, 101
), (
	1, 102
), (
	2, 12
), (
	3, 20
), (
	4, 13
), (
	4, 14
), (
	4, 15
), (
	4, 16
), (
	4, 17
);