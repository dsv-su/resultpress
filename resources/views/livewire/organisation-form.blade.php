<div>

    <div class="container align-self-center">
        <div class="form-inline form-main-search d-flex justify-content-between">
            <label for="header-main-search-text" class="sr-only">{{ __("Search") }}</label>
            <input wire:model="searchTerm" class="form-control w-50 mx-auto" type="search" autocomplete="off"
                   aria-haspopup="true"
                   placeholder="{{ __("Search Organisation") }}"
                   aria-labelledby="header-main-search-form">
        </div>
    </div>
    <!-- Search -->
    <div class="card-body pb-1">
        {{$organisations->onEachSide(1)->links('livewire.pagination')}}
    </div>
    <!-- Organisations list -->
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-8 col-lg-12">
                <h6 class="text-muted">Organisations</h6>
                <ul class="list-group">
                    @foreach($organisations as $spiderorg)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="image-parent" style="max-width:200px;">
                            <img src="{{$spiderorg->logo}}" class="img-fluid" alt="spider">
                        </div>
                        <span>&nbsp;&nbsp;&nbsp;</span>
                        <a href="#" wire:click="editOrg({{$spiderorg->id}})" class="d-block w-100">{{$spiderorg->org}}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <br>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif


    <form wire:submit.prevent="submit" method=POST">
        <h4>Organisation</h4>
        <p>Enter a the Name, Address and a Logo of the organisation. Other attributes are optional or can be added later.</p>
        <div class="card bg-light m-auto">
            <div class="card-body pb-1">
                <!-- org -->
                <div class="form-group mb-1 row">
                    <label for="org" class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Organisation</label>
                    <div class="col-8 col-sm-9 px-1">
                        <input type="text" wire:model="org" placeholder="Organisation" class="form-control form-control-sm" value="{{$org}}">
                        @error('org') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- address -->
                <div class="form-group mb-1 row">
                    <label for="org" class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Address</label>
                    <div class="col-8 col-sm-9 px-1">
                        <textarea type="text" wire:model="address" placeholder="Address" class="form-control form-control-sm"></textarea>
                        @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group mb-1 row">
                    <!-- website -->
                    <label for="website"
                           class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Website</label>
                    <div class="col-8 col-sm-4 px-1">
                        <input type="text" wire:model="website" placeholder="Website"
                               class="form-control form-control-sm">
                        @error('website') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <!-- phone -->
                    <label for="phone"
                           class="col-4 col-sm-1 pl-0 pl-sm-1 pr-1 col-form-label-sm text-right">Phone</label>
                    <div class="col-8 col-sm-4 px-1">
                        <input type="text" wire:model="phone" placeholder="Phone"
                               class="form-control form-control-sm">
                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-group mb-1 row">
                    <!-- contact_project -->
                    <label for="contact_project"
                           class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Contact Project</label>
                    <div class="col-8 col-sm-4 px-1">
                        <input type="text" wire:model="contact_project" placeholder="Contact Project"
                               class="form-control form-control-sm">
                        @error('contact_project') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <!-- phone -->
                    <label for="contact_finance"
                           class="col-4 col-sm-1 pl-0 pl-sm-1 pr-1 col-form-label-sm text-right">Contact Finance</label>
                    <div class="col-8 col-sm-4 px-1">
                        <input type="text" wire:model="contact_finance" placeholder="Contact Finance"
                               class="form-control form-control-sm">
                        @error('contact_finance') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- Placeholder logo -->

                <div class="form-group mb-1 row">
                    <label for="org" class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Logo</label>
                    <div class="col-8 col-sm-4 px-1">

                        <input type="file" wire:model="logo" class="form-control form-control-sm">
                        @error('logo') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>
        </div>
        <div class="px-4 py-3 text-right sm:px-6">
            <button wire:click="newOrg" class="inline-flex justify-center py-2 px-4 btn btn-outline-primary">New</button>
            <button wire:click="saveOrg" class="inline-flex justify-center py-2 px-4 btn btn-outline-primary">Save</button>
        </div>
    </form>



        @if($partners)
        <h4>Users</h4>

        <div class="px-4 py-3 text-right sm:px-6">
            {{-- Create New User}}
            <button class="inline-flex justify-center py-2 px-4 btn btn-outline-primary" wire:click.prevent="adduser()">Add User</button>
            {{--}}
            <button class="inline-flex justify-center py-2 px-4 btn btn-outline-primary" wire:click.prevent="linkuser()">Link New User</button>
        </div>
            @if($linkuseractive)
            <div class="mb-8">
                <label class="inline-block w-32 font-bold">User:</label>
                <select wire:model="luser" class="border shadow p-2 bg-white">
                    @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @foreach($partners as $partner)

                <div class="card bg-light m-auto">
                    <div class="card-body pb-1">
                        <!-- Roles -->
                        <div class="form-group mb-1 row">
                            <label for="firstname" class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Roles</label>
                            <div class="col-8 col-sm-4 px-1">
                            @if(!empty($partner->getRoleNames()))
                                @foreach($partner->getRoleNames() as $v)
                                    <label class="badge badge-success">{{ $v }}</label>
                                @endforeach
                            @endif
                            </div>
                            <!-- Roles -->
                            <div class="form-group mb-1 row">
                                <label for="roles">Select new roles</label>
                                <div class="col-md-8">
                                    <select wire:model="new_roles.{{$loop->index}}" class="form-control" multiple>
                                        @foreach($roles as $permission)
                                            <option value="{{ $permission }}">{{ $permission }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('new_roles.'.$loop->index) <span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <!-- FirstName -->
                        <div class="form-group mb-1 row">
                            <label for="firstname" class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">First Name</label>
                            <div class="col-8 col-sm-4 px-1">
                                <input type="text" wire:model="firstname.{{$loop->index}}" placeholder="First Name" class="form-control form-control-sm">
                                @error('firstname.'.$loop->index) <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="form-group mb-1 row">
                            <!-- email -->
                            <label for="partner_email"
                                   class="col-4 col-sm-3 pl-0 pr-1 col-form-label-sm text-right">Email</label>
                            <div class="col-8 col-sm-4 px-1">
                                <input type="email" wire:model="partner_email.{{$loop->index}}" placeholder="Email"
                                       class="form-control form-control-sm">
                                @error('partner_email.'.$loop->index) <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="px-4 py-3 text-right sm:px-6">
                            <button class="inline-flex justify-center py-2 px-4 btn btn-outline-primary" wire:click.prevent="remove({{$partner->id}})">Unlink</button>
                            <button class="inline-flex justify-center py-2 px-4 btn btn-outline-primary" wire:click.prevent="partner({{$partner->id}},{{$loop->index}})">Update</button>
                        </div>


                    </div>
                </div>
            @endforeach
        @endif

</div>
