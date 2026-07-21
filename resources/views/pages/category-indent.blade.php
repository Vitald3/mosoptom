@foreach($categories as $category)
    <tr style="background: #eee">
        @role('delete')
        <td><input type="checkbox" name="selected[]" value="{{ $category['id'] }}" /></td>
        @endrole
        <td>{{ $parent . ' > ' . (!empty($category->metaLang['name']) ? $category->metaLang['name'] : $category['name']) }} </td>
        <td>{{ $category['sort'] }}</td>
        <td>{{ $category['status'] == 1 ? 'Включено' : 'Выключено' }}</td>
        @role('edit')
        <td><a href="{{asset('admin/category/' . $category['id'])}}"><i class="bx bx-edit-alt"></i></a></td>
        @endrole
    </tr>
    @if($category->children->count())
        @include('pages.category-indent', ['categories' => $category->children, 'parent' => $parent . ' > ' . (!empty($category->metaLang['name']) ? $category->metaLang['name'] : $category['name'])])
    @endif
@endforeach