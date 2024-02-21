<?php

namespace App\Tests;

class MultiBench
{
    public const UUIDS = [
        "eb739d02-bb41-48b5-aeb6-f649fa5926cc",
        "250f56b9-a846-4b18-bbad-5bb84eb7c4a8",
        "9df19d40-345c-4c66-6a7c-a96e075c102a",
        "5f7cc630-bd47-443c-ea25-650b0ef0aeb3",
        "e057ba8c-4566-4c47-38ff-de1f87ecc9c0",
        "e43b10cd-031d-4f36-e463-69854c712a2c",
        "8683a9c2-b499-4f44-45c2-a687cd3d9745",
        "56cdcbc6-9281-4781-6968-8658047de390",
        "ad81a508-20f5-4e6e-73f1-72d30fe6837d",
        "5c77a3fe-575d-4e99-117f-2f6f19ecde2f",
        "16092a97-bd81-46ea-cc04-5cf89a89c6d7",
        "eeaf230a-52a8-43fe-45c5-d8d19f072282",
        "bf713edb-5ad6-4581-1e7a-63c83e564378",
        "3031f62c-01fb-4054-dc0e-cd6aff625c03",
        "bcb50d83-6cbf-4c62-1259-5353ba2b4088",
        "c09c8665-7206-489d-6ae0-fde03639ca61",
        "c5cbe6e4-0074-4b76-55d7-2ac5965f2a02",
        "c7176914-3a52-4be9-66d4-ddefcba35c76",
        "4e894db0-7225-43f1-566f-bd8d5be1dfea",
        "a9da7e32-56bd-4608-0966-00cd9bd7d12d",
        "52541f5a-e2fd-4ec7-6424-dc43caeaefd1",
        "773686ee-6a0a-478b-04b0-f596c59ddbae",
        "32fa0e0d-b9fa-48c9-a416-2264c00f6488",
        "aa9d2255-c002-4283-91d3-e4af2b3dc95e",
        "47e83224-d743-4ec8-781d-2f1d27f6d757",
        "2aaebf70-4d41-44c6-3eaa-6f5d5bc2cfbf",
        "667b530b-6d9b-4051-3cae-fea23bf48474",
        "6b85fccd-d355-4936-fb6a-9a9fa177921a",
        "4f62d336-ab83-4ca2-f6e8-323ccdf77f8f",
        "2e1967f7-bee3-4170-4cd3-5d28ef458a7e",
        "ac41cad6-c0cc-4553-6a12-26918964bc39",
        "12af2319-b5ea-4586-5b27-9f6d612d6c48",
        "0c086cfe-640b-4117-9c22-e3524b4a1272",
        "6e48dabb-a2fe-417a-180e-08e33b573d12",
        "3d533ee1-7a3c-458d-90ee-2516e166ad6a",
        "4f5f6c54-9c4b-492e-1758-c70f74ba4ba3",
        "6fb9bb84-5458-4b17-bd83-154a598b3522",
        "802ff479-db00-46bc-dded-583a4ee2cca8",
        "573f7038-84f0-41c2-277d-8de8d1ff2155",
        "13333004-e816-4191-46f7-145dce855906",
        "d3221add-f572-4a91-4ff9-ec39f438b737",
        "9587c8cf-6f39-4128-1dc4-cb18c55fd891",
        "19a030af-65ac-4332-bfb3-725d9c541dec",
        "dc8e546f-ba55-4dfb-aba8-a3735613012e",
        "f25b1ab7-e511-4cc4-ad9b-79c8c6d9c9b1",
        "85274f50-10d8-4673-00a2-2494a3dd574c",
        "21f1904b-f22f-4514-dc11-49b10088eb95",
        "0abc2ab6-5fd7-46ba-2266-1c107d6e347a",
        "05cbeace-496e-43e3-457a-13da3532e928",
        "4bac233b-a0f3-44b9-e056-c0ec2848f055",
    ];

    private static ?int $PID = null;

    public function benchUuidV4Current()
    {
        foreach (self::UUIDS as $uuid) {
            $uuid[19] = ['8', '9', 'a', 'b', '8', '9', 'a', 'b', 'c' => '8', 'd' => '9', 'e' => 'a', 'f' => 'b'][$uuid[19]] ?? $uuid[19];
        }
    }

    public function benchUuidV4Variant1()
    {
        foreach (self::UUIDS as $uuid) {
            $uuid[19] = ['8', '9', 'a', 'b'][ord($uuid[0]) & 0x3];
        }
    }

    public function benchUuidV4Variant3()
    {
        foreach (self::UUIDS as $uuid) {
            $o = ord($uuid[19]);
            $c = ((int)(($o + 3) / 100)) * 41 + (($o % 97) % 2) + 56;
            $uuid[19] = chr($c);
        }
    }

    public function benchUuidV4Variant4()
    {
        foreach (self::UUIDS as $uuid) {
            $o = ord($uuid[19]) & 0x3;
            $x = (($o + 10) % 12);
            $uuid[19] = chr($x + 31 * ((int)($x / 10)) + 56);
        }
    }

    public function benchUuidV4Variant5()
    {
        foreach (self::UUIDS as $uuid) {
            $uuid[19] = ['8', '9', 'a', 'b'][random_int(0, 3)];
        }
    }

    public function benchUuidV4Variant6()
    {
        foreach (self::UUIDS as $uuid) {
            $uuid[19] = ['8', '9', 'a', 'b'][(int) (time() % 4)];
        }
    }

    public function benchUuidV4Variant7()
    {
        foreach (self::UUIDS as $uuid) {

            if (self::$PID === null) {
                self::$PID = getmypid();
            }

            $uuid[19] = ['8', '9', 'a', 'b'][self::$PID % 4];
        }
    }
    
}
