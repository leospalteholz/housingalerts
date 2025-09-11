class Hearing extends Model
{
    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function region() {
        return $this->belongsTo(Region::class);
    }
}
