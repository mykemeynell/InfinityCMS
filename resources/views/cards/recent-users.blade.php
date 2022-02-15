<!-- Projects table -->
<table class="items-center w-full bg-transparent border-collapse">
    <thead class="thead-light">
    <tr>
        <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 font-semibold text-left">
            Name
        </th>
        <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 font-semibold text-left">
            Email
        </th>
        <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 font-semibold text-left">
            Last Logged In
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs p-4">
                {{ $user->getDisplayName() }}
            </td>
            <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs p-4">
                {{ $user->getEmail() }}
            </td>
            <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs p-4">
                {{ $user->getLastLoggedIn()->format(infinity_datetime_format()) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
