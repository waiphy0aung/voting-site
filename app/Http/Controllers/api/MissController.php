<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Miss;
use App\Models\Vote;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use stdClass;

use function PHPUnit\Framework\isEmpty;

class MissController extends Controller
{
    public function misses(Request $request, Miss $miss)
    {
        try {
            if (!$miss->id) {
                $misses = Miss::with('votes')->get();

                foreach ($misses as $key => $value) {
                    $misses[$key]['voteCount'] = $this->voteDetails($misses[$key]->votes)[0];
                    $misses[$key]['isVote'] = $this->voteDetails($misses[$key]->votes)[1];
                    $misses[$key]['hobby'] = json_decode($misses[$key]->hobby);
                    unset($misses[$key]->votes);
                }

                return response()->json(['data' => $misses, 'status' => 'success'], 200);
            } else {
                $miss['voteCount'] = $this->voteDetails($miss->votes)[0];
                $miss['isVote'] = $this->voteDetails($miss->votes)[1];
                $miss['hobby'] = json_decode($miss->hobby);
                unset($miss->votes);
                return response()->json(['data' => $miss, 'status' => 'success'], 200);
            }
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function miss(Miss $miss)
    {
        try {
            if (!$miss) return response()->json(['data' => 'Miss not found', 'status' => 'error'], 404);
            $miss['voteCount'] = $this->voteDetails($miss->votes)[0];
            $miss['isVote'] = $this->voteDetails($miss->votes)[1];
            $miss['hobby'] = json_decode($miss->hobby);
            unset($miss);
            return response()->json(['data' => $miss, 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = json_decode($request->data);

            $validator = Validator::make(json_decode($request->data, true), [
                'name' => 'required|unique:misses,name|min:2|max:50',
                'height' => 'numeric|required|min:150|min:150|max:200',
                'weight' => 'numeric|required|min:40|max:100',
                'bust' => 'numeric|required|min:10|max:100',
                'waist' => 'numeric|required|min:10|max:100',
                'hips' => 'numeric|required|min:10|max:100',
                'age' => 'numeric|required|min:15|max:60',
                'location' => 'required',
                'hobby' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['data' => $validator->errors(), 'status' => 'fail'], 500);
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = 'miss';
                $localfolder = public_path('firebase-temp-uploads') . '/';
                $fileName = uniqid() . $image->getClientOriginalName();
                $url = '';
                if ($image->move($localfolder, $fileName)) {
                    $uploadedfile = fopen($localfolder . $fileName, 'r');
                    app('firebase.storage')->getBucket()->upload($uploadedfile, ['name' => $path . '/' . $fileName]);
                    unlink($localfolder . $fileName);
                    $url = 'https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/' . $path . '%2F' . $fileName . '?alt=media';
                }
            } else return response()->json(['data' => ['image' => ["Please select image"]], 'success' => 'fail'], 500);

            $miss = Miss::create([
                'name' => $data->name,
                'image' => $url,
                'height' => $data->height,
                'weight' => $data->weight,
                'bust' => $data->bust,
                'waist' => $data->waist,
                'hips' => $data->hips,
                'age' => $data->age,
                'location' => $data->location,
                'hobby' => json_encode($data->hobby)
            ]);

            $miss->hobby = json_decode($miss->hobby);
            /* $miss->voteCount = $this->voteDetails($miss->id, Auth::id())[0]; */
            /* $miss->isVote = $this->voteDetails($miss->id, Auth::id())[1]; */

            return response()->json(['data' => $miss, 'status' => 'success'], 201);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function update(Request $request, Miss $miss)
    {
        try {
            if (!$miss) return response()->json(['data' => 'Miss not found!', 'status' => 'error'], 404);

            $data = json_decode($request->data);

            $validator = Validator::make(json_decode($request->data, true), [
                'name' => 'required|unique:misses,name|min:2|max:50',
                'height' => 'numeric|required|min:150|min:150|max:200',
                'weight' => 'numeric|required|min:40|max:100',
                'bust' => 'numeric|required|min:10|max:100',
                'waist' => 'numeric|required|min:10|max:100',
                'hips' => 'numeric|required|min:10|max:100',
                'age' => 'numeric|required|min:15|max:60',
                'location' => 'required',
                'hobby' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['data' => $validator->errors(), 'status' => 'fail'], 500);
            }

            $url = $miss->image;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = 'miss';
                $localfolder = public_path('firebase-temp-uploads') . '/';
                $fileName = uniqid() . $image->getClientOriginalName();
                if ($image->move($localfolder, $fileName)) {
                    $uploadedfile = fopen($localfolder . $fileName, 'r');
                    app('firebase.storage')->getBucket()->upload($uploadedfile, ['name' => $path . '/' . $fileName]);
                    unlink($localfolder . $fileName);

                    $baseUrl = "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/";
                    $imagePath = str_replace($baseUrl, '', $miss->image);
                    $index = strpos($imagePath, '?');
                    $imagePath = substr($imagePath, 0, $index);
                    $imagePath = preg_replace('/%2F/', '/', $imagePath);
                    $imagePath = preg_replace('/%20/', ' ', $imagePath);
                    app("firebase.storage")->getBucket()->object($imagePath)->delete();

                    $url = 'https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/' . $path . '%2F' . $fileName . '?alt=media';
                }
            }

            $miss->update([
                'name' => $data->name,
                'image' => $url,
                'height' => $data->height,
                'weight' => $data->weight,
                'bust' => $data->bust,
                'waist' => $data->waist,
                'hips' => $data->hips,
                'age' => $data->age,
                'location' => $data->location,
                'hobby' => json_encode($data->hobby)
            ]);
            /* $miss->voteCount = $this->voteDetails($miss->id, Auth::id())[0]; */
            /* $miss->isVote = $this->voteDetails($miss->id, Auth::id())[1]; */

            return response()->json(['data' => $miss, 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function delete(Miss $miss)
    {
        try {
            if (!$miss) return response()->json(['data' => 'Miss not found!', 'status' => 'error'], 404);

            $baseUrl = "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/";
            $imagePath = str_replace($baseUrl, '', $miss->image);
            $index = strpos($imagePath, '?');
            $imagePath = substr($imagePath, 0, $index);
            $imagePath = preg_replace('/%2F/', '/', $imagePath);
            $imagePath = preg_replace('/%20/', ' ', $imagePath);
            app("firebase.storage")->getBucket()->object($imagePath)->delete();

            Vote::where("missId", $miss->id)->delete();
            $miss->delete();
            return response()->json(['data' => $miss->id, 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function voteDetails($votes)
    {
        try {
            $voteCount = new stdClass();
            $isVote = new stdClass();
            foreach ($votes as $key => $vote) {
                $category = Category::find($vote->categoryId)->slug;
                $count = Vote::where('categoryId', $vote->categoryId)->count();
                $find = collect($votes)->where('userId', Auth::id())->where('categoryId', $vote->categoryId);
                $voteCount->$category = $count;
                if (count($find) > 0) {
                    $isVote->$category = true;
                } else {
                    $isVote->$category = false;
                }
            }
            return [$voteCount, $isVote];
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function addMissesData()
    {
        try {
            $arr = json_decode('[
      {
        "name": "Dee",
        "slug": "dee",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fk4tp19omwj_Dee.jpeg?alt=media&token=588163f7-4fa2-44a4-824e-2b9230fe4572",
        "age": 27,
        "height": 171,
        "weight": 45,
        "bust": 34,
        "waist": 24,
        "hips": 36,
        "location": "Mawlamyine",
        "hobby": [
          "Fashion",
          "Thrifting",
          "Music",
          "Philanthropy "
        ],
        "createdAt": {
          "$date": "2023-08-20T05:50:38.883Z"
        },
        "updatedAt": {
          "$date": "2023-08-26T11:36:07.825Z"
        },
        "__v": 0
      },
      {
        "name": "Rosa Tinggaw Gi San",
        "slug": "rosa-tinggaw-gi-san",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fxrsdwhak3k_IMG_20230825_100341_874.jpg?alt=media&token=370fd6a2-fc13-432d-b0dd-8a4b2e2344ac",
        "age": 22,
        "height": 170,
        "weight": 48,
        "bust": 36,
        "waist": 23,
        "hips": 36,
        "location": "Lashio",
        "hobby": [
          "Philanthropy ",
          "Singing "
        ],
        "createdAt": {
          "$date": "2023-08-25T03:35:40.832Z"
        },
        "updatedAt": {
          "$date": "2023-08-26T11:36:21.444Z"
        },
        "__v": 0
      },
      {
        "name": "Pyae Kaung Su Thant",
        "slug": "pyae-kaung-su-thant",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fkj915an8x8p_IMG_20230825_100804_981.jpg?alt=media&token=20422257-b4a2-48fc-b6f7-074b339d3421",
        "age": 27,
        "height": 178,
        "weight": 53,
        "bust": 34,
        "waist": 24,
        "hips": 37,
        "location": "Hpakant",
        "hobby": [
          "Boxing",
          "Acting"
        ],
        "createdAt": {
          "$date": "2023-08-25T03:38:17.363Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T03:38:17.363Z"
        },
        "__v": 0
      },
      {
        "name": "May Thinzar",
        "slug": "may-thinzar",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2F7h01b7j4nin_IMG_20230825_104032_822.jpg?alt=media&token=18e12f8b-ba74-4749-9270-4e012770e29e",
        "age": 18,
        "height": 162,
        "weight": 48,
        "bust": 32,
        "waist": 24,
        "hips": 36,
        "location": "Pyay",
        "hobby": [
          "Acting ",
          "Modelling "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:10:44.823Z"
        },
        "updatedAt": {
          "$date": "2023-08-26T11:36:35.288Z"
        },
        "__v": 0
      },
      {
        "name": "Saw Myat Ei Khine",
        "slug": "saw-myat-ei-khine",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fpgckbjo3s7_IMG_20230825_103248_101.jpg?alt=media&token=8ceb3740-d8bb-4a90-8924-39b454f002fd",
        "age": 23,
        "height": 168,
        "weight": 53,
        "bust": 36,
        "waist": 24,
        "hips": 38,
        "location": "Sittwe ",
        "hobby": [
          "Philanthropy "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:11:49.449Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:11:49.449Z"
        },
        "__v": 0
      },
      {
        "name": "Saung Hay Hman",
        "slug": "saung-hay-hman",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2F3hcb6r2m6wo_IMG_20230825_104247_648.jpg?alt=media&token=08aa44eb-765f-41cb-bbd9-0425e7e32081",
        "age": 20,
        "height": 168,
        "weight": 43,
        "bust": 36,
        "waist": 24,
        "hips": 38,
        "location": "Mogok",
        "hobby": [
          "Philanthropy "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:13:09.100Z"
        },
        "updatedAt": {
          "$date": "2023-08-26T11:36:49.823Z"
        },
        "__v": 0
      },
      {
        "name": "Pandra ",
        "slug": "pandra",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fwxy22ot1tjj_IMG_20230825_104405_026.jpg?alt=media&token=6f828622-0124-4c93-bde1-90d4b38d6e80",
        "age": 22,
        "height": 170,
        "weight": 55,
        "bust": 34,
        "waist": 22,
        "hips": 37,
        "location": "Yangon(East)",
        "hobby": [
          "Gym",
          "Beauty"
        ],
        "createdAt": {
          "$date": "2023-08-25T04:14:16.846Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:14:16.846Z"
        },
        "__v": 0
      },
      {
        "name": "No No May",
        "slug": "no-no-may",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fobyygoc87yi_IMG_20230825_104459_857.jpg?alt=media&token=63c71d47-6003-4da4-898f-8ceec6fbf0e7",
        "age": 20,
        "height": 167,
        "weight": 52,
        "bust": 36,
        "waist": 24,
        "hips": 38,
        "location": "Pathein",
        "hobby": [
          "Philanthropy "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:15:10.189Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:15:10.189Z"
        },
        "__v": 0
      },
      {
        "name": "Amara Bo",
        "slug": "amara-bo",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fwdyd1copc0p_IMG_20230825_104559_479.jpg?alt=media&token=0a1b31a4-e2f3-4cff-a727-ea981fc4ac49",
        "age": 26,
        "height": 181,
        "weight": 53,
        "bust": 34,
        "waist": 23,
        "hips": 37,
        "location": "Keng Tung",
        "hobby": [
          "Philanthropy "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:16:10.760Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:16:10.760Z"
        },
        "__v": 0
      },
      {
        "name": "Mai Zi Latt",
        "slug": "mai-zi-latt",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fepwzh7kg4zh_IMG_20230825_104729_602.jpg?alt=media&token=d0b2f4a2-9d44-4e3e-93fb-3155ffbdbbf0",
        "age": 27,
        "height": 172,
        "weight": 50,
        "bust": 36,
        "waist": 24,
        "hips": 38,
        "location": "Kutkai",
        "hobby": [
          "Acting "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:17:39.520Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:17:39.520Z"
        },
        "__v": 0
      },
      {
        "name": "Seng Pan Htoi",
        "slug": "seng-pan-htoi",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fd65ds088dac_IMG_20230825_104830_744.jpg?alt=media&token=c3b2f901-f94b-4b05-8722-57dc6c046090",
        "age": 20,
        "height": 164,
        "weight": 45,
        "bust": 32,
        "waist": 24,
        "hips": 37,
        "location": "Muse",
        "hobby": [
          "Acting "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:18:39.667Z"
        },
        "updatedAt": {
          "$date": "2023-08-26T11:37:09.606Z"
        },
        "__v": 0
      },
      {
        "name": "El Linn",
        "slug": "el-linn",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fcwulxxyismd_IMG_20230825_104943_844.jpg?alt=media&token=68ac0217-d676-4810-a33f-4fa08695cc82",
        "age": 24,
        "height": 165,
        "weight": 52,
        "bust": 33,
        "waist": 23,
        "hips": 35,
        "location": "Yangon(South)",
        "hobby": [
          "Makeup Art",
          "Modelling ",
          "Acting "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:19:52.643Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:19:52.643Z"
        },
        "__v": 0
      },
      {
        "name": "Ei Kha Kha Maw",
        "slug": "ei-kha-kha-maw",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2F5s8kklt6njv_IMG_20230825_105052_444.jpg?alt=media&token=63461689-69ef-4257-b6b0-9d0e5b7fb371",
        "age": 20,
        "height": 168,
        "weight": 53,
        "bust": 32,
        "waist": 24,
        "hips": 35,
        "location": "Taungoo",
        "hobby": [
          "Modelling "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:21:03.361Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:21:03.361Z"
        },
        "__v": 0
      },
      {
        "name": "Rose Seng Htoi",
        "slug": "rose-seng-htoi",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fqsba275rvdp_IMG_20230825_105159_123.jpg?alt=media&token=56a4089c-e49f-4e49-8bc9-ec6f4fd1b072",
        "age": 23,
        "height": 168,
        "weight": 52,
        "bust": 34,
        "waist": 23,
        "hips": 36,
        "location": "Myitkyina",
        "hobby": [
          "Fashion Design"
        ],
        "createdAt": {
          "$date": "2023-08-25T04:22:07.704Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:22:07.704Z"
        },
        "__v": 0
      },
      {
        "name": "Thin Sandar Pyae Thiha Aung",
        "slug": "thin-sandar-pyae-thiha-aung",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Foe65r5k92yc_IMG_20230825_105326_094.jpg?alt=media&token=c9ec4f70-f4f4-48f4-bfdc-f8ab17eff88f",
        "age": 22,
        "height": 163,
        "weight": 49,
        "bust": 33,
        "waist": 26,
        "hips": 37,
        "location": "Pyin Oo Lwin",
        "hobby": [
          "Modelling ",
          "Motorcycle Racing"
        ],
        "createdAt": {
          "$date": "2023-08-25T04:23:33.908Z"
        },
        "updatedAt": {
          "$date": "2023-08-26T11:37:27.877Z"
        },
        "__v": 0
      },
      {
        "name": "Hsu Myat Thu",
        "slug": "hsu-myat-thu",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fg5hgzqc5g95_IMG_20230825_105425_561.jpg?alt=media&token=2ffd7fc4-2843-4f7e-a73d-ede317e71a27",
        "age": 20,
        "height": 165,
        "weight": 51,
        "bust": 34,
        "waist": 24,
        "hips": 37,
        "location": "Taunggyi",
        "hobby": [
          "Modelling ",
          "Acting "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:24:32.821Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:24:32.821Z"
        },
        "__v": 0
      },
      {
        "name": "Zin Ei Nwe",
        "slug": "zin-ei-nwe",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fabp0dpx47nf_IMG_20230825_105518_724.jpg?alt=media&token=0a8e2b5e-3c3b-4856-9382-ce2d2efc9f7b",
        "age": 18,
        "height": 168,
        "weight": 53,
        "bust": 32,
        "waist": 22,
        "hips": 37,
        "location": "AungBan",
        "hobby": [
          "Fashion Design "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:25:26.884Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:25:26.884Z"
        },
        "__v": 0
      },
      {
        "name": "Wutt Hmone Han",
        "slug": "wutt-hmone-han",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fi15pkccjgkp_IMG_20230825_105614_965.jpg?alt=media&token=5cc9faad-a787-4119-893d-f095670b5f95",
        "age": 21,
        "height": 163,
        "weight": 50,
        "bust": 35,
        "waist": 23,
        "hips": 36,
        "location": "Mandalay",
        "hobby": [
          "Beauty "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:26:33.442Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:26:33.442Z"
        },
        "__v": 0
      },
      {
        "name": "Yu Wady Aung",
        "slug": "yu-wady-aung",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Frgl9afmdphm_IMG_20230825_105725_821.jpg?alt=media&token=f3040dbf-36c3-4a3b-a014-1dcf8fa565f4",
        "age": 23,
        "height": 180,
        "weight": 55,
        "bust": 36,
        "waist": 24,
        "hips": 37,
        "location": "Monywa",
        "hobby": [
          "Beauty "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:27:33.944Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:27:33.944Z"
        },
        "__v": 0
      },
      {
        "name": "Amelia",
        "slug": "amelia",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fmy9iwn7l9j_IMG_20230825_105825_697.jpg?alt=media&token=53104840-8e33-4977-b196-9838ebd63906",
        "age": 25,
        "height": 175,
        "weight": 50,
        "bust": 32,
        "waist": 23,
        "hips": 36,
        "location": "Hpa-An",
        "hobby": [
          "Modelling "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:28:41.380Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:28:41.380Z"
        },
        "__v": 0
      },
      {
        "name": "Phyu Sin Thant",
        "slug": "phyu-sin-thant",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fyl0h8gxx56b_IMG_20230825_105926_658.jpg?alt=media&token=f1e6bb6d-db38-4152-bda6-5a2333037caf",
        "age": 18,
        "height": 167,
        "weight": 45,
        "bust": 32,
        "waist": 22,
        "hips": 35,
        "location": "Yangon(North)",
        "hobby": [
          "Programming "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:29:33.767Z"
        },
        "updatedAt": {
          "$date": "2023-08-26T11:38:01.747Z"
        },
        "__v": 0
      },
      {
        "name": "Lily",
        "slug": "lily",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2F6gyftegtn9_IMG_20230825_110016_580.jpg?alt=media&token=462cd8e8-ee6a-4c41-9bad-a9706af74ddc",
        "age": 27,
        "height": 168,
        "weight": 52,
        "bust": 33,
        "waist": 23,
        "hips": 38,
        "location": "Kawkareik",
        "hobby": [
          "Philanthropy "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:30:26.140Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:30:26.140Z"
        },
        "__v": 0
      },
      {
        "name": "Wint Shwe Sin",
        "slug": "wint-shwe-sin",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fcog35c49crg_IMG_20230825_110111_774.jpg?alt=media&token=8ad7ff49-4144-4c5a-881a-f3a1254fbdae",
        "age": 23,
        "height": 170,
        "weight": 50,
        "bust": 34,
        "waist": 23,
        "hips": 37,
        "location": "Bago",
        "hobby": [
          "Beauty "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:31:18.695Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:31:18.695Z"
        },
        "__v": 0
      },
      {
        "name": "Sasha Viola",
        "slug": "sasha-viola",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fi5xtx853jpp_IMG_20230825_110243_523.jpg?alt=media&token=2d69b379-8f23-4292-8600-67bedae1aa1e",
        "age": 24,
        "height": 167,
        "weight": 51,
        "bust": 36,
        "waist": 24,
        "hips": 38,
        "location": "Nay Pyi Taw ",
        "hobby": [
          "Philanthropy "
        ],
        "createdAt": {
          "$date": "2023-08-25T04:32:51.489Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T04:32:51.489Z"
        },
        "__v": 0
      },
      {
        "name": "Khay Mar",
        "slug": "khay-mar",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2F5p862fu3nkf_IMG_20230825_130535_332.jpg?alt=media&token=7bc28feb-ec89-43b7-9d7e-f7029108baa4",
        "age": 18,
        "height": 172,
        "weight": 51,
        "bust": 34,
        "waist": 24,
        "hips": 36,
        "location": "Bhamo",
        "hobby": [
          "Beauty ",
          "Gym"
        ],
        "createdAt": {
          "$date": "2023-08-25T06:35:47.345Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T06:35:47.345Z"
        },
        "__v": 0
      },
      {
        "name": "Kyi Phyu Khin",
        "slug": "kyi-phyu-khin",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2F8s1zz7ps3cd_IMG_20230825_130653_736.jpg?alt=media&token=4a695747-2431-454b-b495-a595a8915dd2",
        "age": 27,
        "height": 170,
        "weight": 50,
        "bust": 34,
        "waist": 23,
        "hips": 35,
        "location": "Pyinmana",
        "hobby": [
          "Philanthropy "
        ],
        "createdAt": {
          "$date": "2023-08-25T06:37:01.818Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T06:37:01.818Z"
        },
        "__v": 0
      },
      {
        "name": "Nang Nandar Linn",
        "slug": "nang-nandar-linn",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Flh584xk9ky_IMG_20230825_130823_883.jpg?alt=media&token=2228c374-87f8-4875-af8c-b81707f81cd5",
        "age": 19,
        "height": 170,
        "weight": 51,
        "bust": 34,
        "waist": 24,
        "hips": 38,
        "location": "Tachileik",
        "hobby": [
          "Playing Football ",
          "Doing Vlogs",
          "Dancing"
        ],
        "createdAt": {
          "$date": "2023-08-25T06:38:32.448Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T06:38:32.448Z"
        },
        "__v": 0
      },
      {
        "name": "Khun Sett Cho",
        "slug": "khun-sett-cho",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Ffdt6depg8gl_IMG_20230825_131135_582.jpg?alt=media&token=69daaf84-5666-498f-b691-cc872658ff87",
        "age": 27,
        "height": 165,
        "weight": 51,
        "bust": 32,
        "waist": 23,
        "hips": 36,
        "location": "Falam",
        "hobby": [
          "Fashion",
          "Music",
          "Art",
          "Educational Charity Work"
        ],
        "createdAt": {
          "$date": "2023-08-25T06:41:43.886Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T06:41:43.886Z"
        },
        "__v": 0
      },
      {
        "name": "Yoon May Aung",
        "slug": "yoon-may-aung",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fhzda13jm3lp_IMG_20230825_131241_651.jpg?alt=media&token=f7d36cea-ee0b-4767-b2ff-2c5d6bb5c2cc",
        "age": 22,
        "height": 171,
        "weight": 48,
        "bust": 36,
        "waist": 24,
        "hips": 37,
        "location": "Hakha",
        "hobby": [
          "Music ",
          "Dancing "
        ],
        "createdAt": {
          "$date": "2023-08-25T06:42:49.537Z"
        },
        "updatedAt": {
          "$date": "2023-08-26T11:38:25.090Z"
        },
        "__v": 0
      },
      {
        "name": "Pearl Pwint Phyu",
        "slug": "pearl-pwint-phyu",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fx6elkvab9or_IMG_20230825_131353_643.jpg?alt=media&token=4440a389-2627-487b-8cc5-eea463126535",
        "age": 20,
        "height": 168,
        "weight": 48,
        "bust": 32,
        "waist": 24,
        "hips": 37,
        "location": "Amarapura",
        "hobby": [
          "Modelling "
        ],
        "createdAt": {
          "$date": "2023-08-25T06:44:01.594Z"
        },
        "updatedAt": {
          "$date": "2023-08-26T11:38:39.588Z"
        },
        "__v": 0
      },
      {
        "name": "Ei Shwe Eain",
        "slug": "ei-shwe-eain",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2F43or0a7o1ra_IMG_20230825_131447_781.jpg?alt=media&token=62406def-8c05-4529-8f81-fbb626eb3c92",
        "age": 18,
        "height": 167,
        "weight": 52,
        "bust": 34,
        "waist": 24,
        "hips": 38,
        "location": "Kyaukse",
        "hobby": [
          "Music ",
          "Dancing "
        ],
        "createdAt": {
          "$date": "2023-08-25T06:44:56.109Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T06:44:56.109Z"
        },
        "__v": 0
      },
      {
        "name": "Su Myat Noe",
        "slug": "su-myat-noe",
        "image": "https://firebasestorage.googleapis.com/v0/b/miss-mm-53464.appspot.com/o/miss%2Fq8u0dy06m7c_IMG_20230825_131552_911.jpg?alt=media&token=d7df130a-5f2d-431a-8d1d-5579c6b14bbb",
        "age": 21,
        "height": 168,
        "weight": 53,
        "bust": 35,
        "waist": 23,
        "hips": 36,
        "location": "Meikhtila",
        "hobby": [
          "Modelling ",
          "Singing ",
          "Photoshooting"
        ],
        "createdAt": {
          "$date": "2023-08-25T06:46:03.554Z"
        },
        "updatedAt": {
          "$date": "2023-08-25T06:46:03.554Z"
        },
        "__v": 0
      }
    ]');
            foreach ($arr as $key => $value) {
                Miss::create([
                    'name' => $arr[$key]->name,
                    'image' => $arr[$key]->image,
                    'height' => $arr[$key]->height,
                    'weight' => $arr[$key]->weight,
                    'bust' => $arr[$key]->bust,
                    'waist' => $arr[$key]->waist,
                    'hips' => $arr[$key]->hips,
                    'age' => $arr[$key]->age,
                    'location' => $arr[$key]->location,
                    'hobby' => json_encode($arr[$key]->hobby)
                ]);
            }
            return "success";
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }
}
