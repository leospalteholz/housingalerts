class Region extends Model
{
    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function hearings() {
        return $this->hasMany(Hearing::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }
}
