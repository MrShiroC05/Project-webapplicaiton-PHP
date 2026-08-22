-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2026 at 10:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `champion`
--

CREATE TABLE `champion` (
  `champion_id` varchar(4) NOT NULL COMMENT 'champion''s ID',
  `champion_name` varchar(20) DEFAULT 'champion''s name',
  `champion_title` varchar(40) DEFAULT NULL,
  `champion_gender` varchar(1) DEFAULT NULL,
  `champion_story` text NOT NULL DEFAULT 'story of champion',
  `champion_image` text NOT NULL,
  `champion_regionId` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='champion''s information';

--
-- Dumping data for table `champion`
--

INSERT INTO `champion` (`champion_id`, `champion_name`, `champion_title`, `champion_gender`, `champion_story`, `champion_image`, `champion_regionId`) VALUES
('C001', 'Lux', 'Lady of Luminosity', 'F', 'Luxanna—or Lux, as she prefers to be called—grew up in the Demacian city of High Silvermere, along with her older brother Garen. They were born to the prestigious Crownguard family, which had served for generations as protectors of the kings of Demacia. Their grandfather saved the king’s life at the Battle of Storm’s Fang, and their aunt Tianna was named commander of the elite Dauntless Vanguard regiment before Lux was born.\r\n\r\nGaren took to his family’s role with fervor, joining the military when he was still little more than a boy. Lux, in his absence, was expected to help run the family’s many estates—a task she resented, even as a young child. She wanted to explore the world, to see what lay beyond the walls and borders of Demacia. She idolized Garen, but railed against his insistence that she put her own ambitions aside.\r\n\r\nTo the endless frustration of Lux’s tutors, who sought to prepare her for a life of dutiful service to the Crownguard family, she would question their every teaching, examine differing perspectives, and seek out knowledge far beyond what they were prepared for. Even so, few could find it in themselves to stay angry at Lux, with her zest for life and intoxicating optimism.\r\n\r\nLittle did any of them know a time of change was approaching. Magic had once brought Runeterra to the brink of annihilation, and Demacia had been founded as a place where such powers were forbidden. Many of the kingdom’s folktales told of pure hearts turned dark by the lure of magic. Indeed, Lux and Garen’s uncle had been slain by a rogue mage some years earlier.\r\n\r\nAnd there were fearful whispers, rumors from beyond the great mountains, that magic was rising once more in the world…\r\n\r\nRiding home one fateful night, Lux and her horse were attacked by a ravenous sabrewulf pack. In a moment of fear and desperation, the young girl let loose a torrent of magical light from deep within her, routing the beasts but leaving her shivering in fear. Magic, the terror of Demacian myths, was as much a part of Lux as her Crownguard lineage.\r\n\r\nFear and doubt gnawed at her. Would she become evil? Was she an abomination, to be imprisoned or exiled? At the very least, if her powers were discovered, it would see the Crownguard name disgraced forever.\r\n\r\nWith Garen spending more time away from High Silvermere, Lux found herself alone in the halls of their family home. Still, over time, she became more familiar with her magic, and her sleepless nights—fists clenched, willing her inner light to fade—became fewer and fewer. She began experimenting in secret, playing with sunbeams in the courtyards, bending them into solid form, and even creating tiny, glowing figures in her palm. She resolved to keep it a secret, as much as she could.\r\n\r\nWhen she was sixteen, Lux traveled with her parents Pieter and Augatha to their formal residence in the Great City of Demacia, to witness Garen’s investiture into the honored ranks of the Dauntless Vanguard.\r\n\r\nThe city dazzled Lux. It was a monument to the noble ideals of the kingdom, with every citizen protected and cared for; and it was there that Lux learned of the Illuminators, a charitable religious order working to help the sick and the poor. Between her family’s courtly engagements, she became close with a knight of the order named Kahina, who also taught Lux more martial skills, sparring and training with her in the gardens of the Crownguard manor.\r\n\r\nSpending more time in the capital, Lux has finally begun to learn about the wider world—its diversity, and its history. She now understands that the Demacian way of life is not the only way, and with clear eyes she can see her love for her homeland standing alongside her desire to see it made more just… and perhaps a little more accepting of mages like her.', 'upload/champion/crop_6a899e91c50df5.84925923.png', 'R001'),
('C002', 'Garen', 'Might of Demacia', 'M', 'Born into the noble Crownguard family, along with his younger sister Lux, Garen knew from an early age that he would be expected to defend the throne of Demacia with his life. His father, Pieter, was a decorated military officer, while his aunt Tianna was Sword-Captain of the elite Dauntless Vanguard—and both were recognized and greatly respected by King Jarvan III. It was assumed that Garen would eventually come to serve the king’s son in the same manner.\r\n\r\nThe kingdom of Demacia had risen from the ashes of the Rune Wars, and the centuries afterward were plagued with further conflict and strife. One of Garen’s uncles, a ranger-knight in the Demacian military, told young Garen and Lux his tales of venturing outside the kingdom’s walls to protect its people from the dangers of the world beyond.\r\n\r\nHe warned them that, one day, something would undoubtedly end this time of relative peace—whether it be rogue mages, creatures of the abyss, or some other unimaginable horror yet to come.\r\n\r\nAs if to confirm those fears, their uncle was killed in the line of duty by a mage, before Garen turned eleven. Garen saw the pain this brought to his family, and the fear in his young sister’s eyes. He knew then, for certain, that magic was the first and greatest peril that Demacia faced, and he vowed never to let it within their walls. Only by following their founding ideals, and by displaying their unshakeable pride, could the kingdom be kept safe.\r\n\r\nAt the age of twelve, Garen left the Crownguard home in High Silvermere to join the military. As a squire, his days and nights were consumed by training and the study of war, honing his body and mind into a weapon as strong and true as Demacian steel. It was then that he first met young Jarvan IV—the prince who, as king, he would one day serve—among the other recruits, and the two became inseparable.\r\n\r\nIn the years that followed, Garen earned his place in the shieldwall as a warrior of Demacia, and quickly gained a fearsome reputation on the battlefield. By the time he was eighteen, he had served with honor in campaigns along the Freljordian borders, played a key role in purging fetid cultists from the Silent Forest, and fought alongside the valiant defenders of Whiterock.\r\n\r\nKing Jarvan III himself summoned Garen’s battalion back to the Great City of Demacia, honoring them before the royal court in the Hall of Valor. Tianna Crownguard, recently elevated to the role of High Marshal, singled out her nephew in particular, and recommended him for the trials necessary to join the ranks of the Dauntless Vanguard.\r\n\r\nGaren returned home in preparation, and was greeted warmly by Lux and his parents, as well as the common people living on his family’s estate. Though he was pleased to see his sister growing into an intelligent, capable young woman, something about her had changed. He had noticed it whenever he visited, but now Garen wrestled with a real and gnawing suspicion that Lux possessed magical powers… though he never let himself entertain the idea for long. The thought of a Crownguard being capable of the same forbidden sorceries that had slain their uncle was too unbearable to confront.\r\n\r\nNaturally, through courage and skill, Garen won his place among the Vanguard. With his proud family and his good friend the prince looking on, he took his oaths before the throne.\r\n\r\nLux and her mother spent much more time in the capital, in service to the king as well as the humble order of the Illuminators—yet Garen tried to keep his distance as much as possible. Though he loved his sister more than anything else in the world, some small part of him had a hard time getting close to her, and he tried not to think about what he would be forced to do if his suspicions were ever confirmed. Instead, he threw himself into his new duties, fighting and training twice as hard as he had before.\r\n\r\nWhen the new Sword-Captain of the Dauntless Vanguard fell in battle, Garen found himself put forward for command by his fellow warriors, and the nomination was unopposed.\r\n\r\nTo this day, he stands resolute in the defense of his homeland, against all foes. Far more than Demacia\'s most formidable soldier, he is the very embodiment of all the greatest and most noble ideals upon which it was founded.', 'upload/champion/crop_6a899eca7cefe6.78525454.png', 'R001'),
('C003', 'Darius', 'Hand of Noxus', 'M', 'sr', 'upload/champion/crop_6a899f2cb04ef1.91481457.png', 'R002'),
('C004', 'Swain', 'Noxian Grand General', 'M', 'st', 'upload/champion/crop_6a899f6cb41870.42235178.png', 'R002'),
('C005', 'Ahri', 'Nine-Tailed Fox', 'F', 'st', 'upload/champion/crop_6a89a15ab88c10.14952631.png', 'R003'),
('C006', 'Yasuo', 'Unforgiven', 'M', 'st', 'upload/champion/crop_6a89a1a8064953.17358961.png', 'R003'),
('C007', 'Ashe', 'Frost Archer', 'F', 'st', 'upload/champion/crop_6a89a1cc335b51.05044512.png', 'R004'),
('C008', 'Vi', 'Piltover Enforcer', 'F', 'st', 'upload/champion/crop_6a89a207c37915.60534986.png', 'R005'),
('C009', 'Jinx', 'Loose Cannon', 'F', 'st', 'upload/champion/crop_6a89a23a3d80e4.76566025.png', 'R006'),
('C010', 'Viego', 'Ruined King', 'M', 'st', 'upload/champion/crop_6a89a25e98a400.17819811.png', 'R007'),
('C011', 'Sett', 'THE BOSS', 'M', 'Though now a powerful player in Ionia’s flourishing criminal underworld, Sett had humble origins. Born from an Ionian vastaya and a Noxian human, the “half-beast” child was an outcast from the start. His birth appalled his mother’s vastayan community, which expelled the family for violating its tribal norms. The humans of Ionia were no more accepting of the taboo union, though Sett’s father’s infamy as a local pitfighter usually kept them from voicing their disapproval.\r\n\r\nWhat little security the family enjoyed vanished the day Sett’s father disappeared. All of a sudden, those who had bitten their tongues at the sight of young Sett felt free to express their contempt. The boy was bewildered, wondering where his father had gone, and why trouble suddenly seemed to be following him.\r\n\r\nSett grew up quickly, becoming calloused in the face of the taunts and threats he endured, and before long, he began using his fists to silence the insults. When news of his fights reached his mother, she made him swear not to go near the Noxian pits where his father had fought.\r\n\r\nBut the more Sett fought, the more he thought of his father.\r\n\r\nLonging to find the man he only vaguely remembered, Sett snuck away to the pit late one night, after his mother had gone to bed. Immediately, he was enthralled by the spectacle. Scores of Noxian soldiers, fresh to the shores of Ionia, roared with bloodlust from the stands around him. Down in the center of the arena, fighters from all backgrounds and martial disciplines clashed in gruesome duels with a variety of weapons—the winners handsomely paid in Noxian coin. When the event was over, Sett inquired about his father, and learned a hard truth: his father had bought out his contract and left to tour more profitable pits abroad. He had deserted his family, to seek fortune on the other side of the world.\r\n\r\nBurning with rage, Sett asked the arena’s matchmaker for a fight, hoping that somehow his father would return from his tour—and be the opponent standing across the pit from him. The matchmaker assigned the boy a fight on the next card, figuring he would be easy fodder for one of his star combatants.\r\n\r\nSett would prove him wrong.\r\n\r\nFrom the moment he threw his first punch, “The Beast-Boy Bastard” was a pit-fighting sensation. Though Sett had no formal martial arts training, his primal strength and ferocity more than compensated, and he leveled his more technically sound opponents like a battering ram. Never abandoning hope that he might one day fight his father, he soon became the undisputed “King of the Pit”, with a swollen coffer of prize money—and a trail of broken opponents—to his name.\r\n\r\nNight after night, Sett brought money and comforts to his mother, always lying about how he had acquired them. It warmed his calloused heart to see her so proud of his success, no longer forced to toil at menial jobs. Still, Sett couldn’t help but feel he could do better. Being the King of the Pit was good, but being the person who owned the pit… that was where the real money was.\r\n\r\nLate one night, after defending his title in front of a record-breaking crowd, Sett presented his new demands to the Noxian matchmaker and his cronies. He suggested they grant him control of the arena and its revenue. When they refused, Sett barred the doors. Minutes later, the doors re-opened, and the Noxians emerged, badly maimed, with a message on their bloodied lips: the half-beast was the new boss.\r\n\r\nWith the promoters out of the picture, Sett took control of the pit he once fought in. Ionians, who had only recently been conditioned for war, flocked to the arena, paying to satisfy an urge they only now knew they possessed. Sett took full advantage of their newfound bloodlust, accumulating wealth and power beyond his wildest boyhood dreams, as he transformed the pit into the hub of an underground empire of gambling and vice.\r\n\r\nThe half-beast who reigned supreme in the pit now runs his illicit enterprises with the same iron fist. Any time someone challenges his authority, he personally reminds them where they stand. Every punch Sett throws is a blow to his old life of poverty and ostracism, and he intends to make sure that old life stays down.', 'upload/champion/crop_6a89a7b7180157.94810727.png', 'R003'),
('C012', 'Aatrox', 'The world ender', 'M', 'sasdda', 'upload/champion/crop_6a89bc72d9cd71.19358374.png', 'R008');

-- --------------------------------------------------------

--
-- Table structure for table `champion_race`
--

CREATE TABLE `champion_race` (
  `champion_id` varchar(4) NOT NULL,
  `race_id` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `champion_race`
--

INSERT INTO `champion_race` (`champion_id`, `race_id`) VALUES
('C003', 'R001'),
('C004', 'R001'),
('C005', 'R002'),
('C006', 'R001'),
('C007', 'R008'),
('C008', 'R001'),
('C009', 'R001'),
('C010', 'R004'),
('C001', 'R001'),
('C002', 'R001'),
('C011', 'R001'),
('C011', 'R002'),
('C012', 'R009');

-- --------------------------------------------------------

--
-- Table structure for table `race`
--

CREATE TABLE `race` (
  `race_id` varchar(4) NOT NULL,
  `race_name` varchar(20) NOT NULL,
  `race_description` text NOT NULL,
  `race_image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `race`
--

INSERT INTO `race` (`race_id`, `race_name`, `race_description`, `race_image`) VALUES
('R001', 'Human', 'Normal humans', 'upload/race/crop_6a899c220dadf3.90103079.png'),
('R002', 'Vastaya', 'Magical race', 'upload/race/crop_6a899c52162b50.26926414.png'),
('R003', 'Yordle', 'Small magical creatures', 'upload/race/crop_6a899c902c4fd7.35259141.png'),
('R004', 'Undead', 'Living dead', 'upload/race/crop_6a899ce3d66e29.49667731.png'),
('R005', 'Ascended', 'Shuriman Ascended', 'upload/race/crop_6a899d3de2eac8.62663792.png'),
('R006', 'Celestial', 'Cosmic beings', 'upload/race/crop_6a899d64e14ad1.07196044.png'),
('R007', 'Spirit', 'Spiritual beings', 'upload/race/crop_6a899db0de90c8.82425663.png'),
('R008', 'Iceborn', 'Resistant to True Ice', 'upload/race/crop_6a899ddda40e78.79934418.png'),
('R009', 'Darkin', 'Corrupted Ascended', 'upload/race/crop_6a899e1613aa13.25006934.png'),
('R010', 'Construct', 'Artificial beings', 'upload/race/crop_6a899e61c201b2.57716006.png');

-- --------------------------------------------------------

--
-- Table structure for table `region`
--

CREATE TABLE `region` (
  `region_id` varchar(4) NOT NULL,
  `region_name` varchar(30) NOT NULL,
  `region_description` text NOT NULL,
  `region_image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `region`
--

INSERT INTO `region` (`region_id`, `region_name`, `region_description`, `region_image`) VALUES
('R001', 'Demacia', 'Kingdom of justice. A powerful and honorable kingdom built on ideals of justice, duty, and protection. Demacia is known for its elite knights, strong military traditions, and distrust of magic.', 'upload/region/crop_6a89993038b1b5.03754092.png'),
('R002', 'Noxus', 'Expansionist empire. A vast and aggressive empire that values strength, ambition, and individual ability. Noxus welcomes anyone who can prove their worth, regardless of their origin or social status.', 'upload/region/crop_6a89996caaabf5.54797668.png'),
('R003', 'Ionia', 'Land of balance. A collection of provinces surrounded by nature and spiritual energy. Ionia seeks harmony between the physical and spiritual worlds, but its balance has been threatened by war and foreign invasion.', 'upload/region/crop_6a8999a2db6bf1.90144547.png'),
('R004', 'Freljord', 'Frozen north. A harsh and frozen land inhabited by powerful tribes and ancient beings. Its people struggle to survive the unforgiving environment while fighting over the future of the Freljord.', 'upload/region/crop_6a8999db1283f5.53434635.png'),
('R005', 'Piltover', 'City of Progress. A prosperous city known as the City of Progress. Piltover is a center of science, technology, trade, and innovation, powered by the discovery and development of Hextech.', 'upload/region/crop_6a899a1b71e336.66840366.png'),
('R006', 'Zaun', 'Underground city. A sprawling underground city beneath Piltover, filled with industrial factories, dangerous experiments, and powerful chemists. Despite its harsh conditions, Zaun is home to brilliant inventors and ambitious individuals.', 'upload/region/crop_6a899a488c4e73.95887295.png'),
('R007', 'Shadow Isles', 'Cursed Isles. A group of cursed islands consumed by the Black Mist. Once a beautiful kingdom, the Shadow Isles became a land of death and undeath after a magical catastrophe known as the Ruination.', 'upload/region/crop_6a899a71c00377.85447344.png'),
('R008', 'Shurima', 'Ancient Empire. The remnants of a once-great ancient empire that ruled much of the southern continent. Shurima is slowly rising again, bringing its forgotten history, Ascended warriors, and ancient power back into the world.', 'upload/region/crop_6a899a9cad2b23.44095466.png'),
('R009', 'Bilgewater', 'Pirate city. A lawless port city located among dangerous islands. It is home to pirates, smugglers, bounty hunters, and merchants who seek fortune through trade, crime, and dangerous adventures across the seas.', 'upload/region/crop_6a899ae3e19990.80596678.png'),
('R010', 'Targon', 'Celestial mountain. A sacred mountainous region dominated by Mount Targon, a place where mortals can encounter powerful celestial forces. The region is home to several ancient cultures and individuals chosen as vessels of celestial beings.', 'upload/region/crop_6a899b14071f06.42222778.png');

-- --------------------------------------------------------

--
-- Table structure for table `relationship`
--

CREATE TABLE `relationship` (
  `champion_id` varchar(10) NOT NULL,
  `relateChampion_id` varchar(10) NOT NULL,
  `relationship_type` varchar(3) NOT NULL COMMENT 'SIB - sibling, ALS - Allies, RIV - rival, FRI - Friend, ENM - Enemy, TAL - temporary Alliance, etc'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `relationship`
--

INSERT INTO `relationship` (`champion_id`, `relateChampion_id`, `relationship_type`) VALUES
('C001', 'C002', 'SIB'),
('C003', 'C004', 'ALS'),
('C005', 'C006', 'ALS');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `champion`
--
ALTER TABLE `champion`
  ADD PRIMARY KEY (`champion_id`),
  ADD KEY `Test` (`champion_regionId`);

--
-- Indexes for table `champion_race`
--
ALTER TABLE `champion_race`
  ADD KEY `champion` (`champion_id`),
  ADD KEY `race` (`race_id`);

--
-- Indexes for table `race`
--
ALTER TABLE `race`
  ADD PRIMARY KEY (`race_id`);

--
-- Indexes for table `region`
--
ALTER TABLE `region`
  ADD PRIMARY KEY (`region_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `champion`
--
ALTER TABLE `champion`
  ADD CONSTRAINT `Test` FOREIGN KEY (`champion_regionId`) REFERENCES `region` (`region_id`);

--
-- Constraints for table `champion_race`
--
ALTER TABLE `champion_race`
  ADD CONSTRAINT `champion` FOREIGN KEY (`champion_id`) REFERENCES `champion` (`champion_id`),
  ADD CONSTRAINT `race` FOREIGN KEY (`race_id`) REFERENCES `race` (`race_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
