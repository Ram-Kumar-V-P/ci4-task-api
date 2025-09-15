<?php

namespace App\Controllers;

use App\Models\AttachmentModel;
use App\Models\TaskModel;
use CodeIgniter\HTTP\ResponseInterface;

class AttachmentController extends BaseController
{
    public function index(int $taskId)
    {
        if (! (new TaskModel())->find($taskId)) {
            return respondFail('Task not found', ResponseInterface::HTTP_NOT_FOUND);
        }
        $rows = (new AttachmentModel())->where('task_id',$taskId)->orderBy('id','ASC')->findAll();
        return respondSuccess($rows);
    }

    public function upload(int $taskId)
    {
        if (! (new TaskModel())->find($taskId)) {
            return respondFail('Task not found', ResponseInterface::HTTP_NOT_FOUND);
        }
        
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid()) {
            return respondFail('Invalid file', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $originalName = $file->getClientName();
        $sizeBytes   = $file->getSize();
        // Use CodeIgniter's built-in methods to get MIME type
        $mimeType = $file->getMimeType();

        // Whitelist mime types (example)
        $allowed = ['image/jpeg','image/png','application/pdf','text/plain'];
        if (! in_array($mimeType, $allowed, true)) {
            return respondFail('Disallowed file type', ResponseInterface::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        $newName = $file->getRandomName();
        
        // Ensure target directory exists
        $targetDir = WRITEPATH.'uploads/attachments';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }
        
        // Move file using CodeIgniter's move method
        if (!$file->move($targetDir, $newName)) {
            return respondFail('Failed to move uploaded file', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $u  = service('request')->user;
        $id = (new AttachmentModel())->insert([
            'task_id'       => $taskId,
            'user_id'       => $u['id'],
            'original_name' => $originalName,
            'stored_name'   => $newName,
            'mime_type'     => $mimeType,
            'size_bytes'    => $sizeBytes,
        ]);

        return respondSuccess(['id' => $id], ResponseInterface::HTTP_CREATED);
    }

    public function download(int $attachmentId)
    {
        $row = (new AttachmentModel())->find($attachmentId);
        if (! $row) return respondFail('Attachment not found', ResponseInterface::HTTP_NOT_FOUND);

        $path = WRITEPATH.'uploads/attachments/'.$row['stored_name'];
        if (! is_file($path)) return respondFail('File missing', ResponseInterface::HTTP_NOT_FOUND);

        return $this->response->download($path, null)->setFileName($row['original_name']);
    }
}
