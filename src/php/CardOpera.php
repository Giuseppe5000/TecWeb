<?php

require_once "./php/utils.php";

class CardOpera {
    private $opera;
    private $trimSizeName;

    public function __construct($opera, $trimSizeName = 10){
        $this->opera = $opera;
        $this->trimSizeName = $trimSizeName;
    }

    public function getHomeCard() {
        $homeCard = $this->getBaseCard();
        $homeCard = str_replace('{{CARD_ID}}', '', $homeCard);
        return str_replace('{{CARD_HEADER}}', $this->getCardNameHeading("h3"), $homeCard);
    }

    public function getProfileCard() {
        $profileCard = $this->getBaseCard(140, 140);
        $profileCard = str_replace('{{CARD_ID}}', '', $profileCard);
        return str_replace('{{CARD_HEADER}}', $this->getCardNameHeading("h3"), $profileCard);

    }

    public function getHomeTopCard($topPosition) {
        $idTopPosition = $this->getStringIdTopPosition($topPosition);

        $homeTopCard = $this->getBaseCard();
        $homeTopCard = str_replace('{{CARD_ID}}', "id={$idTopPosition}", $homeTopCard);
        $homeTopCard = str_replace('{{CARD_HEADER}}', $this->getCardHeader(), $homeTopCard);

        $spanPosition = "<span>{$topPosition}°</span>";
        return str_replace('{{HEADER}}', $spanPosition . $this->getCardNameHeading("h3"), $homeTopCard);
    }

    public function getNFTCard() {
        $nftCard = $this->getBaseCard();
        $nftCard = str_replace('{{CARD_ID}}', '', $nftCard);
        $nftCard = str_replace('{{CARD_HEADER}}', $this->getCardHeader(), $nftCard);

        $spanPrice = "<span>{$this->opera["prezzo"]}</span>";
        return str_replace('{{HEADER}}', $this->getCardNameHeading("h2") . $spanPrice, $nftCard);
    }


    private function getBaseCard($width = 200, $height = 200) {
        $card = '';
        $card .= '<div class="card" {{CARD_ID}}>';
        $card .= '<a href="singolo-nft.php?id='.$this->opera["id"].'">';
        $card .= '{{CARD_HEADER}}';
        $card .= '<img src="./' . $this->opera["path"] . '.webp" width="' . $width . '" height="' . $height . '">';
        $card .= '</a>';
        $card .= '</div>';
        return $card;
    }

    private function getCardHeader() {
        return '<div class="head-card"> {{HEADER}} </div>';
    }

    private function getCardNameHeading($headingNumber) {
        return "<{$headingNumber}>" . trimName($this->opera["nome"], $this->trimSizeName)  . "</{$headingNumber}>";
    }

    private function getStringIdTopPosition($topPosition) {
        switch($topPosition) {
        case 1: return "primo";
        case 2: return "secondo";
        case 3: return "terzo";
        }
    }
}
