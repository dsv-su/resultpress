<?php

namespace App\Http\Livewire;

use App\Comment;
use App\Project;
use App\ProjectUpdate;
use App\Notifications\NewComment;
use Livewire\Component;

class Comments extends Component
{
    public $comments;
    public $comment;
    public $editingCommentId;
    public $editingCommentBody;
    public $visible;
    public $commentable_type;
    public $commentable_id;

    public function mount( $commentable_type, $commentable_id )
    {
        $this->commentable_type = $commentable_type;
        $this->commentable_id = $commentable_id;
        $this->comments = $commentable_type::find($commentable_id)->comments;
    }

    public function addComment()
    {
        $object = $this->commentable_type::find($this->commentable_id);
        
        if(auth()->user()->hasRole('Partner')) {
            $this->visible = true;
        } else {
            $this->visible = false;
        }

        $validated = $this->validate([
            'comment' => 'required|max:255',
            'visible' => 'required',
        ]);

        $comment = Comment::create([
            'body' => $this->comment,
            'meta' => [ 'ip' => request()->ip(), 'user_agent' => request()->userAgent() ],
            'visible' => $this->visible,
            'user_id' => auth()->id(),
            'commentable_id' => $this->commentable_id,
            'commentable_type' => $this->commentable_type,
        ]);

        if ($this->commentable_type == Project::class) {
            $object->project_partner->each(function ($partner) use ($comment, $object) {
                $partner->user->notify(new NewComment($comment, $object));
            });
    
            $object->project_owner->each(function ($owner) use ($comment, $object) {
                $owner->user->notify(new NewComment($comment, $object));
            });
        } else if ($this->commentable_type == ProjectUpdate::class) {
            $object->project->project_partner->each(function ($partner) use ($comment, $object) {
                $partner->user->notify(new NewComment($comment, $object->project));
            });
    
            $object->project->project_owner->each(function ($owner) use ($comment, $object) {
                $owner->user->notify(new NewComment($comment, $object->project));
            });
        }

        $this->comments->push($comment);
        $this->comment = '';
        $this->visible = true;
        session()->flash('message', 'Comment successfully added.');
    }

    public function removeComment(Comment $comment)
    {
        $comment->delete();
        $this->comments = $this->comments->where('id', '!=', $comment->id);
    }

    public function editComment(Comment $comment)
    {
        $this->editingCommentId = $comment->id;
        $this->editingCommentBody = $comment->body;
        $this->visible = $comment->visible;
    }

    public function updateComment()
    {
        $validated = $this->validate([
            'editingCommentBody' => 'required|max:255',
        ]);

        $comment = Comment::find($this->editingCommentId);
        $comment->update([
            'body' => $this->editingCommentBody,
        ]);

        $this->comments = $this->comments->map(function ($comment) {
            if ($comment->id === $this->editingCommentId) {
                $comment->body = $this->editingCommentBody;
            }

            return $comment;
        });

        $this->editingCommentId = null;
        $this->editingCommentBody = null;
        $this->visible = true;
        // Flash a message, add comment id to session.
        session()->flash('message', sprintf('Comment %s successfully updated.', $comment->id));
    }

    public function cancelEdit()
    {
        $this->editingCommentId = null;
        $this->editingCommentBody = null;
        $this->visible = true;
    }

    public function resetErrors()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.comments');
    }
}
