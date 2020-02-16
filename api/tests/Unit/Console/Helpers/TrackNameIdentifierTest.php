<?php

namespace Tests\Unit\Console\Helpers;

use App\Console\Helpers\TrackNameIdentifier;
use Tests\TestCase;

class TrackNameIdentifierTest extends TestCase
{

    /**
     * @test
     * @dataProvider provideTestData
     */
    public function it_gets_name_from_lyrics($contents, $expected): void
    {
        $identifier = new TrackNameIdentifier();

        $this->assertEquals($expected, $identifier->getTrackNameFromContents($contents));
    }

    public function provideTestData(): array
    {
        return [
            [
                <<<TXT


                **Nai aundha chain audio file**

                **_

                Nai aundha chain

                **_

                (Haay Sakina haay Masuma) x2

                (Ilayya, ilayya) x2 (Ya Bunayya) x2

                Haay Masuma, Haay Sakina
                TXT,
                'Nai Aundha Chain'
            ],
            [
                <<<TXT


                **_AY MERE MAASUM ASGHAR AUDIO FILE_**

                **_ _**

                **_AY MERE MAASUM ASGHAR_**



                LUT GAYI MAA KARBALAA ME, HAAYE BETA KYA KARE

                JAB MERA ASGHAR NAHI PHIR, KHAALI JULAA KYAA KARE...



                AY MERE MAASUM ASGHAR (x2), RAAT AAYI GHAR CHALO
                TXT,
                'Ay Mere Maasum Asghar'
            ],
            [
                <<<TXT


                **_Beti Ali  ki audio file_**

                **_ _**

                **_Beti_** ** _Ali ki_**



                Shaam hai shaam (x5)



                Shaam aa rahi hai dil per hazaro, Sadme uthaye beti Ali ki (x3)


                Haste hai saare baazar waale (x2)

                Roti hai haaye beti Ali ki
                TXT,
                'Beti Ali Ki'
            ],
            [
                <<<TXT


                Kaash abbas audio file



                Kaash abbas na maray jaatey



                Kaash abbas na maray jaatey (x5)



                1.     Is tarha lagh ti naa akber key kalejey mein sinah,
                TXT,
                'Kaash Abbas'
            ],
            [
                <<<TXT


                **_Blood Will Write Hussain audio_**

                **_Blood Will Write Hussain lyrics video_**

                **_Blood Will Write Hussain_**



                You can bomb us, shoot us, kill us, but we’ll never feel the pain

                If you stop our lips from moving, through our blood we’ll write Hussain



                TXT,
                'Blood Will Write Hussain'
            ],
            [
                <<<TXT
                **_

                **_**Abad-e huwi karbobala video file**

                **_

                Abad-e huwi karbobala

                **_

                (Abad-e huwi karbobala, ujra madina. Barbad-e huwi mai) x2

                  1. Zainab ko narasaya muharram ka mahina
                TXT,
                'Abad-E Huwi Karbobala'
            ],
            [
                <<<TXT


                NOTE FOR RECITERS : This nauha contains "Doray". Doray (Tehtay lafz) are words
                not recited in a tone, during the course of recitation. Doray are usually
                recited during processions OR where there is a high response (matam) from the
                people. It would be advisable to avoid during normal mehfil recitations. These
                are marked with "------".

                **Shaam chali – audio file**

                **_Shaam chali

                **_

                (Shaam chali, me shaam chali) x2

                  1. (pardes me gar lutwaake, par tera din bachaake) x2 me shaam chali nana
                TXT,
                'Shaam Chali'
            ],
            [
                <<<TXT

                **_Mere maajaaye hussain audio file _**

                **_Mere maajaaye hussain_**



                (mere maajaye hussain) x2



                (Saani e zahra pukaari mere maajaye hussain) x3

                TXT,
                'Mere Maajaaye Hussain'
            ],
            [
                <<<TXT

                **_Marjaungi baba audio  file_**

                **_Marjaungi_** ** _baba_**



                (Marjaungi baba) x2

                (saraiki speech)



                (ghabraoongi, baba) x3

                TXT,
                'Marjaungi Baba'
            ],
            [
                <<<TXT
                **_

                Qafla salaar hai audio file

                Qafla salaar hai

                **_

                Kis qadar dushwaar hai ye fasla tokh hai bemaar hai zanjeer hai

                Qafla salaar hai zen-ol-iba tokh hai bemaar hai zanjeer hai
                TXT,
                'Qafla Salaar Hai'
            ],
            [
                <<<TXT

                **_Halmin_** ** _ nasirin_**



                Assalaamu ala-shayb il-khadeeb

                Assalamu alal khaddit-tareeb

                Assalaamu alal badanis-saleeb

                TXT,
                'Halmin Nasirin'
            ],
        ];
    }
}
