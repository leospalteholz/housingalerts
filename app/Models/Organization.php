class Organization extends Model
{
    protected $fillable = ['name', 'slug', 'contact_email'];

    public function users() {
        return $this->hasMany(User::class);
    }

    public function regions() {
        return $this->hasMany(Region::class);
    }

    public function hearings() {
        return $this->hasMany(Hearing::class);
    }
}
