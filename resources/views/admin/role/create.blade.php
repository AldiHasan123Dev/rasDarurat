@extends('layouts.admin')
@section('style')
    <style>
        ul {
        list-style: none;
        padding: 0;
        margin: 0;
        }

        .parent {
        margin-top: 20px;
        }

        .parent label {
        font-weight: bold;
        }

        .child-checkbox {
        margin-left: 20px;
        }

    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card p-3">
                    <form action="{{ route('role-access.store') }}" method="post" class="row">
                        @csrf
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="name">Role Name</label>
                                <input type="text" name="name" id="name" class="form-control w-50">
                            </div>
                            <div class="mb-2">
                                <hr class="py-0">
                                <ul class="parent">
                                    @foreach ($menus as $menu)
                                    <li style="float: left">
                                        <input type="checkbox" id="parent-{{ $menu->id }}" class="parent-checkbox" />
                                        <label for="parent-{{ $menu->id }}">{{ $menu->title }}</label>
                                        <ul>
                                            @foreach ($menu->submenus as $item)
                                            <li>
                                                <input type="checkbox" name="sub_menu_id[]" id="child-{{ $item->id }}" value="{{ $item->id }}" class="child-checkbox" />
                                                <label for="child-{{ $item->id }}">{{ $item->title }}</label>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success mt-2" onclick="return confirm('are you sure?')">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        const parentCheckboxes = document.querySelectorAll('.parent-checkbox');
        const childCheckboxes = document.querySelectorAll('.child-checkbox');

        parentCheckboxes.forEach(parentCheckbox => {
        parentCheckbox.addEventListener('click', () => {
            const isChecked = parentCheckbox.checked;
            const parentList = parentCheckbox.parentElement.querySelector('ul');
            const childCheckboxes = parentList.querySelectorAll('.child-checkbox');
            childCheckboxes.forEach(childCheckbox => {
            childCheckbox.checked = isChecked;
            });
        });
        });

        childCheckboxes.forEach(childCheckbox => {
        childCheckbox.addEventListener('click', () => {
            const parentList = childCheckbox.closest('ul');
            const parentCheckbox = parentList.parentElement.querySelector('.parent-checkbox');
            const childCheckboxes = parentList.querySelectorAll('.child-checkbox');
            const isChecked = [...childCheckboxes].every(checkbox => checkbox.checked);
            parentCheckbox.checked = isChecked;
        });
        });

    </script>
@endsection
