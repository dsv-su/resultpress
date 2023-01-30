<div class="mt-3">
    <div class="card">
        <div class="card-header">
            <h3>Comments</h3>
        </div>
        <div class="card-body">
            @foreach ($comments as $comment)
                @if (session()->has('message') && session('message') == sprintf('Comment %s successfully updated.', $comment->id))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        Comment successfully updated.
                    </div>
                @endif
                @if ($editingCommentId == $comment->id)
                    <div class="media">
                        <div class="form-group row">
                            <div class="col-sm-12">
                                @error('editingCommentBody') 
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert" wire:poll.5000ms>
                                        <strong>Error!</strong> {{ $message }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" wire:click="resetErrors">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div> 
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <textarea wire:model="editingCommentBody" class="form-control" rows="3" placeholder="Edit comment"></textarea>
                            </div>
                            <div class="col-sm-12">
                                <button type="button" wire:click.prevent="updateComment" class="btn btn-primary mt-2">Update</button>
                                <button type="button" wire:click.prevent="cancelEdit" class="btn btn-secondary mt-2">Cancel</button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="media p-2 @if(!$comment->user->hasRole('Partner')) pl-5 border-left @endif">
                        <img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="user" width="50" class="rounded-circle">
                        <div class="media-body d-flex flex-row justify-content-between">
                            <div class="ml-3">
                                <div class="media-title mb-1">{{ $comment->user->name }} (@if($comment->user->hasRole('Partner')) Partner @else SPIDER @endif)</div>
                                <div class="text-muted small">{{ $comment->created_at->diffForHumans() }}</div>
                                <div class="text-small mt-1">{{ $comment->body }}</div>
                            </div>
                            @if (!auth()->user()->hasRole('Partner'))
                                <div class="media-options d-flex">
                                    <a href="#" wire:click.prevent="removeComment({{ $comment->id }})" class="text-danger" onclick="confirm('Confirm delete?') || event.stopImmediatePropagation(); return false;"><i class="fas fa-trash"></i></a>
                                    <a href="#" wire:click.prevent="editComment({{ $comment->id }})" class="text-success ml-2"><i class="fas fa-edit"></i></a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                <hr>
            @endforeach
            @if ($comments->count() == 0)
                <div class="alert alert-info">
                    No comments yet.
                </div>
            @endif
            <div class="form-group row">
                <div class="col-md-12">
                    <textarea wire:model="comment" class="form-control" rows="3" placeholder="Add a comment"></textarea>
                </div>

                <div class="col-md-12">
                    <button wire:click="addComment" class="btn btn-primary btn-sm mt-2">Add Comment</button>
                </div>
                <div class="col-md-12 mt-3">
                    @error('comment') 
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" wire:poll.5000ms>
                            <strong>Error!</strong> {{ $message }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" wire:click="resetErrors">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div> 
                    @enderror
                    @if (session()->has('message') && session('message') == 'Comment added successfully.')
                        <div class="alert alert-success alert-dismissible fade show" role="alert" wire:poll.5000ms>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            {{ session('message') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
