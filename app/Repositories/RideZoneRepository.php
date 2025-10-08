<?php

namespace App\Repositories;

use App\Contracts\Repositories\RideZoneRepositoryInterface;
use App\Models\RideZone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RideZoneRepository implements RideZoneRepositoryInterface
{
    public function __construct(protected RideZone $rideZone) {}

    public function add(array $data): string|object
    {
        $rideZone = $this->rideZone->newInstance();
        foreach ($data as $key => $column) {
            $rideZone[$key] = $column;
        }
        $rideZone->save();
        return $rideZone;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->rideZone->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->rideZone->get();
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);

        return $this->rideZone->withCount(['stores', 'deliverymen'])
            ->when(isset($key), function ($q) use ($key) {
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $rideZone = $this->rideZone->find($id);
        foreach ($data as $key => $column) {
            $rideZone[$key] = $column;
        }
        $rideZone->save();
        return $rideZone;
    }

    public function delete(string $id): bool
    {
        $rideZone = $this->rideZone->find($id);
        $rideZone->translations()->delete();
        $rideZone->delete();

        return true;
    }

    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model
    {
        return $this->rideZone->withoutGlobalScope('translate')->where($params)->first();
    }

    public function getAll(): Collection
    {
        return $this->rideZone->all();
    }

    public function getWithCoordinateWhere(array $params): ?Model
    {
        return $this->rideZone->withoutGlobalScopes()->selectRaw("*,ST_AsText(ST_Centroid(`coordinates`)) as center")->where($params)->first();
    }

    public function getExportList(Request $request): Collection
    {
        $key = explode(' ', $request['search']);
        return $this->rideZone->withCount(['stores', 'deliverymen'])
            ->when(isset($key), function ($q) use ($key) {
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->get();
    }

    public function getLatest(array $relations = []): ?Model
    {
        return $this->rideZone->with($relations)->latest()->first();
    }

    public function rideZoneModuleSetupUpdate(string $id, array $data, array $moduleData): bool|string|object
    {
        $rideZone = $this->rideZone->find($id);
        foreach ($data as $key => $column) {
            $rideZone[$key] = $column;
        }
        $rideZone->modules()->sync($moduleData);
        $rideZone->save();
        return $rideZone;
    }

    public function getWithCountLatest(array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->rideZone->withCount($relations)->latest()->paginate($dataLimit);
    }

    public function getActiveListExcept(array $params): Collection
    {
        return $this->rideZone->whereNot($params)->active()->get();
    }
}
