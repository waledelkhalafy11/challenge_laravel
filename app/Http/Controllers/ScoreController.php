<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScoreController extends Controller
{
   
    public function getScore ( Request $request ) {
        $url = 'https://api.github.com/users/'.$request->username.'/events/public' ;
        $response =json_decode(Http::get( $url ));
        $events = array();
        $score =  0 ;
        
        if (!empty($response)) {
            
            if(isset($response->message) && $response->message == "Not Found" ){

                return response()->json(['message' => 'This User is not Found' ] , 202, [], JSON_PRETTY_PRINT);

            }else{

                foreach ($response as $index => $event) {

                    switch ($event->type) {
                        case 'PushEvent':
                            array_push($events , (object)[
                                'type' => $event->type,
                                'repository' => $event->repo->name,
                                'date' => $event->created_at,
                                'points'=> 10
                            ]);
                            $score = $score + 10 ;
                            break;

                        case 'PullRequestEvent':
                            array_push($events ,(object)[
                                'type' => $event->type,
                                'repository' => $event->repo->name,
                                'date' => $event->created_at,
                                'points'=> 5
                            ]);
                            $score = $score + 5 ;
    
                            break;

                        case 'IssueCommentEvent':
                            array_push($events ,(object)[
                                'type' => $event->type,
                                'repository' => $event->repo->name,
                                'date' => $event->created_at,
                                'points'=> 4
                            ]);
                            $score = $score + 4 ;
                            break;
                                    
                        default:
                        array_push($events ,(object)[
                                'type' => $event->type,
                                'repository' => $event->repo->name,
                                'date' => $event->created_at,
                                'points'=> 1
                            ]);
                            $score ++ ;
                            break;
                    }
    
                }
                return response()->json(['data' => $events , 'score' => $score] , 200, [], JSON_PRETTY_PRINT);

            }

        }else{

            return response()->json(['message' => 'This User has no events' ] , 202, [], JSON_PRETTY_PRINT);
        }

    }


}
