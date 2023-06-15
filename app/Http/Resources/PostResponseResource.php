<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResponseResource extends JsonResource
 {
    /**
    * Transform the resource into an array.
    *
    * @return array<string, mixed>
    */

    public function toArray( Request $request ): array
 {
        parent::toArray( $request );
        $reponse = [
            'fileUniqueId'=>$this["file_id"],
            'fileId'=>$this["id"],
            'fileType'=>$this["file_type"],
            'fileSize'=>$this["file_size"],
            'fileUploader'=>$this["file_uploader"],
            'fileDownloadCount'=>$this["file_downloads"],
            'fileParentPath'=>$this["file_parent_path"],
            'fileDescription'=>$this["file_description"],
            'fileThumbnail'=>$this["file_thumbnails"],
            'fileCreatedAt'=>$this["created_at"],
            'fileRemoteId'=>$this["file_remote_id"],
            'fileDownloadLink'=>$this["file_download_link"],
            'fileCaption'=>$this["file_caption"]
        ];
        return $reponse;
    }
}
