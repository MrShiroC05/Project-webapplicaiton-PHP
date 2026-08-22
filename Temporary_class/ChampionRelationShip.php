<?php 

    class TPChampionRelationShip {
        private $championName_1;
        private $championName_2;
        private $championId_1;
        private $championId_2;
        private $relationType;

        public function __construct($championId_1, $championId_2, $championName_1, $championName_2, $relationType) {
            $this->championId_1 = $championId_1;
            $this->championId_2 = $championId_2;
            $this->championName_1 = $championName_1;
            $this->championName_2 = $championName_2;
            $this->relationType = $relationType;
        }

        public function getChampionId_1() {
            return $this->championId_1;
        }
        public function getChampionId_2(){
            return $this->championId_2;
        }
        public function getChampionName_1(){
            return $this->championName_1;
        }
        public function getChampionName_2(){
            return $this->championName_2;
        }
        public function getRelationType(){
            switch ($this->relationType) {
                case "SIB":
                    return "Sibling";
                case "ALS":
                    return "Allies";
                case "RIV":
                    return "Rivals";
                case "FRI":
                    return "Friends";
                case "ENM":
                    return "Enemies";
                case "TAL":
                    return "temporary alliance";
                default:
                    return "Unknown";
            }
        }
    }
?>