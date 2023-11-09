<?php
namespace App\Http\Controllers;

use App\Restorant;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use DB;
use App\Post;
use App\User;
use route;

class FeedController extends Controller
{
	public function index(Request $request){
		try{
			if($request->is_ajax){
			if($request->search){
				$searchData = DB::table('posts')->select('posts.id','posts.image','posts.video','posts.status','posts.created_at', 'users.name as user_name','users.id as user_id')
                              ->leftJoin('users', 'users.id', '=', 'posts.user_id')
                              ->where('users.name', 'LIKE', '%' . $request->search  . '%')->paginate(10)->appends([
							    'search' => $request->search,
							]); 
			}
			else{
				$searchData = DB::table('posts')->select('posts.id','posts.image','posts.video','posts.status','posts.created_at', 'users.name as user_name','users.id as user_id')
                              ->leftJoin('users', 'users.id', '=', 'posts.user_id')->paginate(10)->appends([
							    'search' => '',
							]); 
			}
               
               $view = view('feeds.searchList', ['searchData' => $searchData])->render();
               return response()->json(
                [
                    'html' => $view,
                    'link' =>  $searchData->links()->render()
                ]
               );
            }
			$getAllFeeds = DB::table('posts')->select('posts.id','posts.image','posts.video','posts.status','posts.created_at','users.name as user_name','users.id as user_id')
                                 ->leftJoin('users', 'users.id', '=', 'posts.user_id')
                                 ->orderBy('posts.id','desc')
                                 ->paginate(10)->appends([
							    'search' => '',
							]); 
	    	return view('feeds.index',compact('getAllFeeds'));
		}catch(Exception $ex){
		 return $ex->getMessage();
		}
	}
	public function edit($id){
		try{
			$feeds = DB::table('posts')->select('users.name as user_name','users.image','posts.*',                   'master_cities.city_name as location_name')
                    ->leftJoin('users', 'users.id', '=', 'posts.user_id')
                    ->leftJoin('master_cities', 'master_cities.id', '=', 'users.place')
			        ->where('posts.id',$id)->first();
         return view('feeds.edit',compact('feeds'));
		}catch(Exception $ex){
		  return $ex->getMessage();	
		}
	}
	public function update(Request $request){
		try{
			$id = $request->id;
			$post = $request->all();
			$postRecord = Post::find($id);
            $storeImges = $postRecord['image'];
            $storeVidoes = $postRecord['video'];
            $imagesP=[];
            if($request->hasfile('image')){
                foreach($request->file('image') as $file)
                {            
                    $randomNum = rand(10,100);
                    $img = time().$randomNum.'.'.$file->getClientOriginalExtension();
                    $imagesP[] = $img;
                    $destinationPath = base_path('public/post');
                    $file->move($destinationPath, $img);
                }
                    $NewImage = implode(',', $imagesP);
                    $post['image'] =  $storeImges.','.$NewImage;
               }
            $videoPath=[];
            if($request->hasfile('video')){
                foreach($request->file('video') as $file)
                {
                    $randomNum = rand(10,100);
                    $vid = time().$randomNum.'.'.$file->getClientOriginalExtension();
                    $videoPath[] = $vid;
                    $destinationPath = base_path('public/post');
                    $file->move($destinationPath, $vid);
                }
                    $NewVideo = implode(',', $videoPath);
                    $post['video'] =  $storeVidoes.','.$NewVideo;
               }   
               $updatePost = Post::find($id)->update($post);
               if($updatePost){
               	  return redirect()->route('feeds.index')->withStatus(__('Feed successfully updated.'));
               }else{
                 return redirect()->route('feeds.index')->withStatus(__('Something went wrong .'));
               }

		}catch(Exception $ex){
			return $ex->getMessage();
		}
	}
	public function delete($id){
		try{
          $deletePost = Post::find($id)->delete();
          if(!empty($deletePost)){
            return redirect()->route('feeds.index')->withStatus(__('Feed successfully deleted.'));
          }else{
            return redirect()->route('feeds.index')->withStatus(__('Feed successfully deleted.'));
          }
		}catch(Exception $ex){
			return $ex->getMessage();
		}
	}
	public function updateStatus(Request $request){
		try{
           $post = Post::find($request->id);
           $post->status = $request->status;
           $post->save();
           return redirect()->back()->withStatus(__('Feed status updated successfully.'));
		}catch(Exception $ex){
			return redirect()->back()->withError(__('Failed to update feed status: ' . $ex->getMessage()));
		}
	}
	public function viewFeedDetails($id){
		try{
			$postData = Post::select('posts.id as post_id','posts.image','posts.video','posts.created_at','users.name as user_name','posts.status','users.id as user_id',           'users.email')
			             ->leftJoin('users', 'users.id', '=', 'posts.user_id')
			             ->where('posts.id',$id)
			             ->first();
			 $likes = User::select('users.name as user_name','users.id as user_id')
			         ->leftJoin('follow_unfollows', 'follow_unfollows.follower_id', '=', 'users.id')
			         ->where(['post_id' => $id , 'type' => 1])->get()->toArray();
	         $comments = User::select('users.name as user_name','comment')
	                     ->leftJoin('comments', 'comments.user_id', '=', 'users.id')
	                     ->where(['post_id' => $id])->get()->toArray();
			 return view('feeds.view',compact('postData','likes','comments'));
		}catch(Exception $ex){
			return $ex->getMessage();
		}
	}
}
?>