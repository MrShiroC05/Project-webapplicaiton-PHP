<?php
include '../Temporary_class/ChampionRelationShip.php';

class Database {
    protected $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getConnection() {
        return $this->conn;
    }
}

class RegionRepository extends Database {
    public function getAll() {
        $result = $this->conn->query("SELECT * FROM region ORDER BY region_id ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($regionId) {
        $stmt = $this->conn->prepare("SELECT * FROM region WHERE region_id = ?");
        $stmt->bind_param('s', $regionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getNextId() {
        $result = $this->conn->query("SELECT region_id FROM region ORDER BY CAST(SUBSTRING(region_id, 2) AS UNSIGNED) DESC LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = (int) preg_replace('/\D/', '', $row['region_id']);
            return 'R' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        }

        return 'R001';
    }

    public function create($data) {
        $regionId = $data['region_id'] ?? $this->getNextId();
        $regionName = $data['region_name'] ?? '';
        $regionDescription = $data['region_description'] ?? '';
        $regionImage = $data['region_image'] ?? null;

        if ($regionImage !== null) {
            $stmt = $this->conn->prepare("INSERT INTO region (region_id, region_name, region_description, region_image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $regionId, $regionName, $regionDescription, $regionImage);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO region (region_id, region_name, region_description) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $regionId, $regionName, $regionDescription);
        }

        return $stmt->execute();
    }

    public function update($regionId, $data) {
        $regionName = $data['region_name'] ?? '';
        $regionDescription = $data['region_description'] ?? '';
        $regionImage = $data['region_image'] ?? null;

        if ($regionImage !== null) {
            $stmt = $this->conn->prepare("UPDATE region SET region_name = ?, region_description = ?, region_image = ? WHERE region_id = ?");
            $stmt->bind_param('ssss', $regionName, $regionDescription, $regionImage, $regionId);
        } else {
            $stmt = $this->conn->prepare("UPDATE region SET region_name = ?, region_description = ? WHERE region_id = ?");
            $stmt->bind_param('sss', $regionName, $regionDescription, $regionId);
        }

        return $stmt->execute();
    }

    public function delete($regionId) {
        $stmt = $this->conn->prepare("DELETE FROM region WHERE region_id = ?");
        $stmt->bind_param('s', $regionId);
        return $stmt->execute();
    }
}

class RaceRepository extends Database {
    public function getAll() {
        $result = $this->conn->query("SELECT * FROM race ORDER BY race_id ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($raceId) {
        $stmt = $this->conn->prepare("SELECT * FROM race WHERE race_id = ?");
        $stmt->bind_param('s', $raceId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getNextId() {
        $result = $this->conn->query("SELECT race_id FROM race ORDER BY CAST(SUBSTRING(race_id, 2) AS UNSIGNED) DESC LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = (int) preg_replace('/\D/', '', $row['race_id']);
            return 'R' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        }

        return 'R001';
    }

    public function create($data) {
        $raceId = $data['race_id'] ?? $this->getNextId();
        $raceName = $data['race_name'] ?? '';
        $raceDescription = $data['race_description'] ?? '';
        $raceImage = $data['race_image'] ?? null;

        $stmt = $this->conn->prepare("INSERT INTO race (race_id, race_name, race_description, race_image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $raceId, $raceName, $raceDescription, $raceImage);
        return $stmt->execute();
    }

    public function update($raceId, $data) {
        $raceName = $data['race_name'] ?? '';
        $raceDescription = $data['race_description'] ?? '';
        $raceImage = $data['race_image'] ?? null;

        if ($raceImage !== null) {
            $stmt = $this->conn->prepare("UPDATE race SET race_name = ?, race_description = ?, race_image = ? WHERE race_id = ?");
            $stmt->bind_param('ssss', $raceName, $raceDescription, $raceImage, $raceId);
        } else {
            $stmt = $this->conn->prepare("UPDATE race SET race_name = ?, race_description = ? WHERE race_id = ?");
            $stmt->bind_param('sss', $raceName, $raceDescription, $raceId);
        }

        return $stmt->execute();
    }

    public function delete($raceId) {
        $stmt = $this->conn->prepare("DELETE FROM race WHERE race_id = ?");
        $stmt->bind_param('s', $raceId);
        return $stmt->execute();
    }
}

class ChampionRepository extends Database {
    public function getAll() {
        $result = $this->conn->query("SELECT * FROM champion ORDER BY champion_id ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAllWithRaceNames() {
        $sql = "
            SELECT c.*, GROUP_CONCAT(r.race_name ORDER BY r.race_name SEPARATOR ', ') AS race_names
            FROM champion c
            LEFT JOIN champion_race cr ON cr.champion_id = c.champion_id
            LEFT JOIN race r ON r.race_id = cr.race_id
            GROUP BY c.champion_id
            ORDER BY c.champion_id ASC
        ";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAllWithDetails() {
        $sql = "
            SELECT c.*, r.region_name,
                GROUP_CONCAT(DISTINCT race.race_name ORDER BY race.race_name SEPARATOR ', ') AS race_names
            FROM champion c
            LEFT JOIN region r ON r.region_id = c.champion_regionId
            LEFT JOIN champion_race cr ON cr.champion_id = c.champion_id
            LEFT JOIN race ON race.race_id = cr.race_id
            GROUP BY c.champion_id
            ORDER BY c.champion_id ASC
        ";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getRacesByChampionId($championId) {
        $stmt = $this->conn->prepare("SELECT race.* FROM race JOIN champion_race cr ON cr.race_id = race.race_id WHERE cr.champion_id = ? ORDER BY race.race_name ASC");
        $stmt->bind_param('s', $championId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($championId) {
        $stmt = $this->conn->prepare("SELECT * FROM champion WHERE champion_id = ?");
        $stmt->bind_param('s', $championId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getNextId() {
        $result = $this->conn->query("SELECT champion_id FROM champion ORDER BY CAST(SUBSTRING(champion_id, 2) AS UNSIGNED) DESC LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = (int) preg_replace('/\D/', '', $row['champion_id']);
            return 'C' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        }

        return 'C001';
    }

    public function getAllWithRegionId($regionId){
        $stmt = $this->conn->prepare("SELECT c.* FROM champion c WHERE c.champion_regionId = ? ORDER BY c.champion_id ASC");
        $stmt->bind_param('s', $regionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllWithRace($raceId){
        $stmt = $this->conn->prepare("SELECT c.*
            FROM champion c
            JOIN champion_race cr 
                ON c.champion_id = cr.champion_id
            WHERE cr.race_id = ?
            GROUP BY c.champion_id
            ORDER BY c.champion_id ASC");
        $stmt->bind_param('s', $raceId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data) {
        $championId = $data['champion_id'] ?? $this->getNextId();
        $championName = $data['champion_name'] ?? '';
        $championTitle = $data['champion_title'] ?? '';
        $championGender = $data['champion_gender'] ?? '';
        $championRegion = $data['champion_regionId'] ?? $data['champion_region'] ?? '';
        $championStory = $data['champion_story'] ?? '';
        $championImage = $data['champion_image'] ?? null;

        $stmt = $this->conn->prepare("INSERT INTO champion (champion_id, champion_name, champion_title, champion_gender, champion_regionId, champion_story, champion_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss', $championId, $championName, $championTitle, $championGender, $championRegion, $championStory, $championImage);
        return $stmt->execute();
    }

    public function update($championId, $data) {
        $championName = $data['champion_name'] ?? '';
        $championTitle = $data['champion_title'] ?? '';
        $championGender = $data['champion_gender'] ?? '';
        $championRegion = $data['champion_regionId'] ?? $data['champion_region'] ?? '';
        $championStory = $data['champion_story'] ?? '';
        $championImage = $data['champion_image'] ?? null;

        if ($championImage !== null) {
            $stmt = $this->conn->prepare("UPDATE champion SET champion_name = ?, champion_title = ?, champion_gender = ?, champion_regionId = ?, champion_story = ?, champion_image = ? WHERE champion_id = ?");
            $stmt->bind_param('sssssss', $championName, $championTitle, $championGender, $championRegion, $championStory, $championImage, $championId);
        } else {
            $stmt = $this->conn->prepare("UPDATE champion SET champion_name = ?, champion_title = ?, champion_gender = ?, champion_regionId = ?, champion_story = ? WHERE champion_id = ?");
            $stmt->bind_param('ssssss', $championName, $championTitle, $championGender, $championRegion, $championStory, $championId);
        }

        return $stmt->execute();
    }

    public function delete($championId) {
        $stmt = $this->conn->prepare("DELETE FROM champion WHERE champion_id = ?");
        $stmt->bind_param('s', $championId);
        return $stmt->execute();
    }

    public function getNameById($championId){
        $stmt = $this->conn->prepare("SELECT champion_name FROM champion WHERE champion_id = ?");
        $stmt->bind_param('s', $championId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            return $row['champion_name'];
        }
        return null;
    }
}

class ChampionRaceRepository extends Database {
    public function getByChampionId($championId) {
        $stmt = $this->conn->prepare("SELECT race_id FROM champion_race WHERE champion_id = ?");
        $stmt->bind_param('s', $championId);
        $stmt->execute();
        $result = $stmt->get_result();
        $raceIds = [];
        while ($row = $result->fetch_assoc()) {
            $raceIds[] = $row['race_id'];
        }
        return $raceIds;
    }

    public function getByRaceId($raceId) {
        $stmt = $this->conn->prepare("SELECT champion_id FROM champion_race WHERE race_id = ?");
        $stmt->bind_param('s', $raceId);
        $stmt->execute();
        $result = $stmt->get_result();
        $championIds = [];
        while ($row = $result->fetch_assoc()) {
            $championIds[] = $row['champion_id'];
        }
        return $championIds;
    }

    public function sync($championId, $selectedRaceIds) {
        $selectedRaceIds = array_values(array_unique(array_filter(array_map('trim', (array) $selectedRaceIds), function ($value) {
            return $value !== '';
        })));

        $this->conn->query("DELETE FROM champion_race WHERE champion_id = '" . $this->conn->real_escape_string($championId) . "'");

        if (empty($selectedRaceIds)) {
            return true;
        }

        $placeholders = implode(',', array_fill(0, count($selectedRaceIds), '?'));
        $sql = "SELECT race_id FROM race WHERE race_id IN (" . $placeholders . ")";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $types = str_repeat('s', count($selectedRaceIds));
        $stmt->bind_param($types, ...$selectedRaceIds);
        $stmt->execute();
        $result = $stmt->get_result();

        $validRaceIds = [];
        while ($row = $result->fetch_assoc()) {
            $validRaceIds[] = $row['race_id'];
        }
        $stmt->close();

        foreach ($validRaceIds as $raceId) {
            $insertStmt = $this->conn->prepare("INSERT INTO champion_race (champion_id, race_id) VALUES (?, ?)");
            $insertStmt->bind_param('ss', $championId, $raceId);
            $insertStmt->execute();
            $insertStmt->close();
        }

        return true;
    }
}

class ChampionRelationShip extends Database {
    public function create($data) {
        // Champion IDs are C001-style strings, so relationship columns must stay VARCHAR.
        $championId = $data['champion_id'] ?? '';
        $relatedChampionId = $data['related_champion_id'] ?? '';
        $relationshipType = $data['relationship_type'] ?? '';

        $stmt = $this->conn->prepare("INSERT INTO relationship (champion_id, relateChampion_id, relationship_type) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $championId, $relatedChampionId, $relationshipType);
        return $stmt->execute();
    }

    public function delete($championId, $relatedChampionId) {
        $stmt = $this->conn->prepare("DELETE FROM relationship WHERE (champion_id = ? AND relateChampion_id = ?) OR (champion_id = ? AND relateChampion_id = ?)");
        $stmt->bind_param('ssss', $championId, $relatedChampionId, $relatedChampionId, $championId);
        return $stmt->execute();
    }

    public function update($championId, $relatedChampionId, $relationshipType) {
        $stmt = $this->conn->prepare("UPDATE relationship SET relationship_type = ? WHERE (champion_id = ? AND relateChampion_id = ?) OR (champion_id = ? AND relateChampion_id = ?)");
        $stmt->bind_param('sssss', $relationshipType, $championId, $relatedChampionId, $relatedChampionId, $championId);
        return $stmt->execute();
    }

    public function getByChampionPair($championId, $relatedChampionId) {
        $stmt = $this->conn->prepare("SELECT relationship_type FROM relationship WHERE (champion_id = ? AND relateChampion_id = ?) OR (champion_id = ? AND relateChampion_id = ?)");
        $stmt->bind_param('ssss', $championId, $relatedChampionId, $relatedChampionId, $championId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getListRelationById($id){
        $stmt = $this->conn->prepare("SELECT r.relationship_type, c.champion_id AS related_champion_id, c.champion_name AS related_champion_name, c.champion_image AS related_champion_image
        FROM relationship AS r JOIN champion AS c
        ON c.champion_id = CASE
        WHEN r.champion_id = ?
        THEN r.relateChampion_id ELSE r.champion_id END WHERE (r.champion_id = ? OR r.relateChampion_id = ?)");
        $stmt->bind_param('sss', $id, $id, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $relations = [];
        while ($row = $result->fetch_assoc()) {
            $relations[] = $row;
        }
        return $relations;
    }
}