INSERT INTO `reward_action` (
	`id` ,
	`name` ,
	`points` ,
	`rate_limit` ,
	`rate_limit_duration` ,
	`count_limit` ,
	`team_credit` ,
	`created_at` ,
	`updated_at`
)
VALUES (
	1, 'création de compte', 200, 0, 0, 0, 0, NOW(), NOW() 
), (
	2, 'newsletter', 150, 0, 0, 0, 0, NOW(), NOW() 
), (
	3, 'newsletter partenaires', 150, 0, 0, 0, 0, NOW(), NOW() 
), (
	4, 'pseudo', 100, 0, 0, 0, 0, NOW(), NOW() 
), (
	5, 'avatar', 150, 0, 0, 0, 0, NOW(), NOW() 
),  (
	6, 'adresse', 150, 0, 0, 0, 0, NOW(), NOW() 
),  (
	7, 'ville', 75, 0, 0, 0, 0, NOW(), NOW() 
),  (
	8, 'téléphone', 150, 0, 0, 0, 0, NOW(), NOW() 
),  (
	9, "nombre d'enfants", 75, 0, 0, 0, 0, NOW(), NOW() 
),  (
	10, 'validation de compte', 100, 0, 0, 0, 0, NOW(), NOW() 
),  (
	11, "centres d'intérêts", 100, 0, 0, 0, 0, NOW(), NOW() 
),  (
	12, 'inscription à un jeu', 100, 0, 0, 0, 0, NOW(), NOW() 
),  (
	13, 'partage par mail', 0, 0, 0, 0, 0, NOW(), NOW() 
),  (
	14, 'partage par FB Wall', 0, 0, 0, 0, 0, NOW(), NOW() 
),  (
	15, 'partage par invitation FB', 0, 0, 0, 0, 0, NOW(), NOW() 
),  (
	16, 'partage par twitter', 0, 0, 0, 0, 0, NOW(), NOW() 
),  (
	17, 'partage par google', 0, 0, 0, 0, 0, NOW(), NOW() 
),  (
	20, 'parrainage inscription', 250, 0, 0, 0, 0, NOW(), NOW() 
),  (
	25, 'bonus anniversaire', 250, 0, 0, 0, 0, NOW(), NOW() 
),  (
	30, 'Quiz : Bonnes réponses', 0, 0, 0, 0, 0, NOW(), NOW() 
),  (
	100, 'badge bronze', 200, 0, 0, 0, 0, NOW(), NOW() 
),  (
	101, 'badge argent', 200, 0, 0, 0, 0, NOW(), NOW() 
),  (
	102, 'badge or', 200, 0, 0, 0, 0, NOW(), NOW() 
);