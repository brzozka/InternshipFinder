<?php

declare(strict_types=1);

namespace Internships\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Response;
use Internships\Enums\CompanyStatus;
use Internships\Enums\Permission;
use Internships\Http\Requests\Api\GetCompaniesRequest;
use Internships\Http\Requests\Api\GetManagedCompaniesRequest;
use Internships\Http\Requests\CompanyRequest;
use Internships\Http\Resources\CompanyResource;
use Internships\Models\Company;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CompanyController extends Controller
{
    public function index(GetCompaniesRequest $request): Response
    {
        return $request->list();
    }

    public function manage(GetManagedCompaniesRequest $request): Response
    {
        return $request->list();
    }

    public function create(): Response
    {
        return inertia(
            "Company/Create",
        );
    }

    /**
     * @throws UnknownProperties
     */
    public function store(CompanyRequest $request): RedirectResponse
    {
        $request->data();

        return redirect()->route("company-manage")
            ->with("success", "status.company_created");
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Company $company, GetCompaniesRequest $request): Response
    {
        $this->authorize("show", $company);
        session(["view-source" => url()->previous()]);

        return $this->list($request)->with(
            "selectedCompany",
            new CompanyResource($company),
        );
    }

    public function close(): RedirectResponse
    {
        $source = session("view-source");

        if (Str::is(route("index"), $source)
            || Str::is(route("company-manage"), $source)
            || Str::is(route("index") . "/?*", $source)
            || Str::is(route("company-manage") . "/?*", $source)) {
            return Redirect::to($source)->withInput();
        }

        return Redirect::to(route("index"));
    }

    /**
     * @throws AuthorizationException
     */
    public function verify(Company $company): RedirectResponse
    {
        $this->authorize(Permission::ManageCompanies);

        $company->update([
            "status" => CompanyStatus::Verified,
        ]);

        return redirect()->route("company-manage")
            ->with("success", "status.company_verified");
    }

    /**
     * @throws AuthorizationException
     */
    public function delete(Company $company): RedirectResponse
    {
        $this->authorize("destroy", $company);
        $company->delete();

        return redirect()->route("company-manage")
            ->with("success", "status.company_deleted");
    }
}